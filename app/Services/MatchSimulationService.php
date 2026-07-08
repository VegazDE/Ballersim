<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MatchSimulationService
{
    public function simulateFixture(Fixture $fixture): Fixture
    {
        $fixture->loadMissing(['homeTeam.players', 'awayTeam.players', 'season']);

        $homeState = $this->buildTeamState($fixture->homeTeam, true);
        $awayState = $this->buildTeamState($fixture->awayTeam, false);
        $matchReport = $this->simulateMatch($fixture, $homeState, $awayState);

        $fixture->forceFill([
            'status' => Fixture::STATUS_FINISHED,
            'home_score' => $matchReport['score']['home'],
            'away_score' => $matchReport['score']['away'],
            'home_xg' => (int) round($matchReport['stats']['home']['xg'] * 10),
            'away_xg' => (int) round($matchReport['stats']['away']['xg'] * 10),
            'match_report' => $matchReport,
            'simulated_at' => now(),
        ])->save();

        return $fixture->refresh();
    }

    public function simulateMatchday(?int $seasonId = null, ?int $leagueId = null, ?int $divisionId = null, ?int $matchday = null): array
    {
        $scope = Fixture::query()
            ->where('status', Fixture::STATUS_SCHEDULED)
            ->when($seasonId !== null, fn ($query) => $query->where('season_id', $seasonId))
            ->when($leagueId !== null, fn ($query) => $query->where('league_id', $leagueId))
            ->when($divisionId !== null, fn ($query) => $query->where('division_id', $divisionId));

        if ($matchday === null) {
            $firstFixture = (clone $scope)
                ->orderBy('kickoff_at')
                ->orderBy('matchday')
                ->first(['season_id', 'matchday']);

            if (! $firstFixture) {
                return ['season_id' => null, 'matchday' => null, 'count' => 0];
            }

            $seasonId = (int) $firstFixture->season_id;
            $matchday = (int) $firstFixture->matchday;
        }

        $fixtures = Fixture::query()
            ->with(['homeTeam.players', 'awayTeam.players', 'season'])
            ->where('status', Fixture::STATUS_SCHEDULED)
            ->when($seasonId !== null, fn ($query) => $query->where('season_id', $seasonId))
            ->when($leagueId !== null, fn ($query) => $query->where('league_id', $leagueId))
            ->when($divisionId !== null, fn ($query) => $query->where('division_id', $divisionId))
            ->when($matchday !== null, fn ($query) => $query->where('matchday', $matchday))
            ->orderBy('kickoff_at')
            ->orderBy('id')
            ->get();

        return DB::transaction(function () use ($fixtures, $seasonId, $matchday): array {
            $count = 0;

            foreach ($fixtures as $fixture) {
                $this->simulateFixture($fixture);
                $count++;
            }

            return [
                'season_id' => $seasonId,
                'matchday' => $matchday,
                'count' => $count,
            ];
        });
    }

    public function simulatePendingFixtures(?int $seasonId = null, ?int $leagueId = null, ?int $divisionId = null, ?int $matchday = null, ?int $limit = null): int
    {
        $query = Fixture::query()
            ->with(['homeTeam.players', 'awayTeam.players', 'season'])
            ->where('status', Fixture::STATUS_SCHEDULED)
            ->when($seasonId !== null, fn ($builder) => $builder->where('season_id', $seasonId))
            ->when($leagueId !== null, fn ($builder) => $builder->where('league_id', $leagueId))
            ->when($divisionId !== null, fn ($builder) => $builder->where('division_id', $divisionId))
            ->when($matchday !== null, fn ($builder) => $builder->where('matchday', $matchday))
            ->orderBy('kickoff_at')
            ->orderBy('matchday')
            ->orderBy('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return DB::transaction(function () use ($query): int {
            $count = 0;

            foreach ($query->get() as $fixture) {
                $this->simulateFixture($fixture);
                $count++;
            }

            return $count;
        });
    }

    private function simulateMatch(Fixture $fixture, array $homeState, array $awayState): array
    {
        $duration = max(5, (int) config('baller_manager.match_duration_minutes', 7));
        $homeXg = $this->calculateExpectedGoals($homeState, $awayState, true, $duration);
        $awayXg = $this->calculateExpectedGoals($awayState, $homeState, false, $duration);

        $homeGoals = $this->poissonSample($homeXg);
        $awayGoals = $this->poissonSample($awayXg);

        $events = [];
        $events = array_merge(
            $events,
            $this->buildSubstitutionEvents($homeState, $awayState, $duration, $homeGoals - $awayGoals, 'home'),
            $this->buildSubstitutionEvents($awayState, $homeState, $duration, $awayGoals - $homeGoals, 'away'),
            $this->buildGoalEvents($duration, $homeGoals, $awayGoals, $homeState, $awayState)
        );

        usort($events, static function (array $left, array $right): int {
            return [$left['minute'] ?? 0, $left['type'] ?? ''] <=> [$right['minute'] ?? 0, $right['type'] ?? ''];
        });

        return [
            'season_id' => $fixture->season_id,
            'matchday' => $fixture->matchday,
            'score' => [
                'home' => $homeGoals,
                'away' => $awayGoals,
            ],
            'stats' => [
                'home' => [
                    'xg' => round($homeXg, 2),
                    'shots' => max($homeGoals + random_int(2, 7), (int) round($homeXg * 4.5)),
                    'possession' => $this->calculatePossession($homeState, $awayState, true),
                ],
                'away' => [
                    'xg' => round($awayXg, 2),
                    'shots' => max($awayGoals + random_int(2, 7), (int) round($awayXg * 4.5)),
                    'possession' => $this->calculatePossession($awayState, $homeState, false),
                ],
            ],
            'home' => $homeState,
            'away' => $awayState,
            'events' => $events,
        ];
    }

    private function buildTeamState(?Team $team, bool $isHome): array
    {
        $formation = $this->normalizeFormation($team?->formation ?? '4-3-3');
        $tactics = [
            'formation' => $formation,
            'mentality' => $this->normalizeTactic($team?->mentality ?? 'balanced', ['defensive', 'balanced', 'attacking']),
            'pressing' => $this->clampStat($team?->pressing ?? 50),
            'tempo' => $this->clampStat($team?->tempo ?? 50),
            'substitution_style' => $this->clampStat($team?->substitution_style ?? 50),
            'line_height' => $this->clampStat($team?->line_height ?? 50),
        ];

        $players = $team?->players
            ? $team->players->sortByDesc(fn ($player) => $this->playerImpactScore($player))->values()
            : collect();

        $slots = $this->formationSlots($formation);
        $usedIds = [];
        $lineup = [];

        foreach ($slots as $role => $count) {
            for ($index = 0; $index < $count; $index++) {
                $player = $this->pickBestPlayerForRole($players, $usedIds, $role);

                if ($player) {
                    $usedIds[] = $player->id;
                    $lineup[] = $this->formatPlayerSlot($player, $role, true);
                    continue;
                }

                $lineup[] = $this->formatPlaceholderSlot($role);
            }
        }

        $bench = [];

        foreach ($players as $player) {
            if (in_array($player->id, $usedIds, true)) {
                continue;
            }

            $bench[] = $this->formatPlayerSlot($player, $this->normalizePosition($player->primary_position), false);
        }

        return [
            'team_id' => $team?->id,
            'team_name' => $team?->name,
            'is_home' => $isHome,
            'tactics' => $tactics,
            'lineup' => $lineup,
            'bench' => array_slice($bench, 0, 7),
            'ratings' => [
                'attack' => $this->calculateAttackRating($lineup, $tactics, $isHome),
                'defense' => $this->calculateDefenseRating($lineup, $tactics, $isHome),
                'overall' => round(collect($lineup)->avg('impact_score') ?: 45, 2),
            ],
            'subs' => [],
        ];
    }

    private function buildSubstitutionEvents(array &$teamState, array &$opponentState, int $duration, int $goalDiff, string $side): array
    {
        if (count($teamState['bench']) === 0 || count($teamState['lineup']) === 0) {
            return [];
        }

        $events = [];
        $windows = [max(4, intdiv($duration, 2)), max(5, $duration - 2)];
        $subsUsed = 0;

        foreach ($windows as $minute) {
            if ($subsUsed >= 3 || count($teamState['bench']) === 0) {
                break;
            }

            $attackMode = $goalDiff < 0 || $teamState['tactics']['mentality'] === 'attacking';
            $outIndex = $this->selectSubstitutionOutIndex($teamState['lineup'], $attackMode);
            $outPlayer = $teamState['lineup'][$outIndex];
            $inIndex = $this->selectSubstitutionInIndex($teamState['bench'], $outPlayer['role'], $attackMode);
            $inPlayer = $teamState['bench'][$inIndex];

            if ($this->playerImpactValue($inPlayer) <= $this->playerImpactValue($outPlayer) - 1.25) {
                continue;
            }

            $teamState['lineup'][$outIndex] = [
                ...$inPlayer,
                'role' => $outPlayer['role'],
                'is_starting' => true,
            ];

            array_splice($teamState['bench'], $inIndex, 1);
            $teamState['subs'][] = [
                'minute' => $minute,
                'out' => $outPlayer,
                'in' => $inPlayer,
            ];

            $events[] = [
                'type' => 'substitution',
                'minute' => $minute,
                'team' => $teamState['team_name'],
                'side' => $side,
                'out' => $outPlayer['name'],
                'in' => $inPlayer['name'],
            ];

            $teamState['ratings']['attack'] = round($teamState['ratings']['attack'] + (($this->playerImpactValue($inPlayer) - $this->playerImpactValue($outPlayer)) / 4), 2);
            $teamState['ratings']['defense'] = round($teamState['ratings']['defense'] + (($this->playerImpactValue($inPlayer) - $this->playerImpactValue($outPlayer)) / 5), 2);

            $subsUsed++;
        }

        return $events;
    }

    private function buildGoalEvents(int $duration, int $homeGoals, int $awayGoals, array $homeState, array $awayState): array
    {
        $events = [];
        $homeMinutes = $this->generateEventMinutes($homeGoals, $duration);
        $awayMinutes = $this->generateEventMinutes($awayGoals, $duration);

        foreach ($homeMinutes as $index => $minute) {
            $scorer = $this->pickScorer($homeState['lineup']);
            $assistant = $this->pickAssist($homeState['lineup'], $scorer['player_id'] ?? null);

            $events[] = [
                'type' => 'goal',
                'minute' => $minute,
                'side' => 'home',
                'team' => $homeState['team_name'],
                'player' => $scorer['name'],
                'assist' => $assistant['name'] ?? null,
                'score' => $index + 1,
            ];
        }

        foreach ($awayMinutes as $index => $minute) {
            $scorer = $this->pickScorer($awayState['lineup']);
            $assistant = $this->pickAssist($awayState['lineup'], $scorer['player_id'] ?? null);

            $events[] = [
                'type' => 'goal',
                'minute' => $minute,
                'side' => 'away',
                'team' => $awayState['team_name'],
                'player' => $scorer['name'],
                'assist' => $assistant['name'] ?? null,
                'score' => $index + 1,
            ];
        }

        return $events;
    }

    private function calculateExpectedGoals(array $teamState, array $opponentState, bool $isHome, int $duration): float
    {
        $attack = (float) $teamState['ratings']['attack'];
        $defense = (float) $opponentState['ratings']['defense'];
        $mentality = $this->mentalityAttackModifier((string) $teamState['tactics']['mentality']);
        $tempo = ((int) $teamState['tactics']['tempo'] - 50) / 140;
        $pressing = ((int) $teamState['tactics']['pressing'] - 50) / 160;
        $venue = $isHome ? 0.18 : -0.04;

        $base = 0.72 + (($attack - $defense) / 18) + $mentality + $tempo + $pressing + $venue;

        return max(0.15, min(4.2, $base * max(0.75, $duration / 7)));
    }

    private function calculateAttackRating(array $lineup, array $tactics, bool $isHome): float
    {
        $base = collect($lineup)->avg('impact_score') ?: 45;
        $roleBoost = collect($lineup)->sum(static fn (array $slot): float => match ($slot['role']) {
            'FW' => 0.35,
            'MF' => 0.18,
            'DF' => 0.06,
            default => 0.02,
        });

        return round($base + $roleBoost + $this->mentalityAttackModifier((string) $tactics['mentality']) + (($tactics['tempo'] - 50) / 120) + ($isHome ? 0.85 : 0), 2);
    }

    private function calculateDefenseRating(array $lineup, array $tactics, bool $isHome): float
    {
        $base = collect($lineup)->avg('impact_score') ?: 45;
        $roleBoost = collect($lineup)->sum(static fn (array $slot): float => match ($slot['role']) {
            'GK' => 0.45,
            'DF' => 0.28,
            'MF' => 0.08,
            default => 0.03,
        });

        return round($base + $roleBoost + $this->mentalityDefenseModifier((string) $tactics['mentality']) + (($tactics['line_height'] - 50) / 150) + (($tactics['pressing'] - 50) / 220) + ($isHome ? 0.45 : 0), 2);
    }

    private function generateEventMinutes(int $count, int $duration): array
    {
        if ($count <= 0) {
            return [];
        }

        $minutes = [];

        for ($index = 0; $index < $count; $index++) {
            $minutes[] = random_int(1, $duration);
        }

        sort($minutes);

        return $minutes;
    }

    private function pickScorer(array $lineup): array
    {
        $candidates = collect($lineup)->filter(static fn (array $slot): bool => ($slot['player_id'] ?? null) !== null)->values();

        if ($candidates->isEmpty()) {
            return $lineup[0] ?? [
                'player_id' => null,
                'name' => 'Unknown',
                'role' => 'MF',
                'impact_score' => 40,
            ];
        }

        $weighted = $candidates->map(static function (array $slot): array {
            $weight = match ($slot['role']) {
                'FW' => 3.4,
                'MF' => 1.8,
                'DF' => 0.65,
                default => 0.2,
            };

            return [
                'slot' => $slot,
                'weight' => max(0.4, $weight + ($slot['impact_score'] / 35)),
            ];
        })->values();

        return $this->weightedPick($weighted->all())['slot'];
    }

    private function pickAssist(array $lineup, ?int $scorerId): ?array
    {
        $candidates = collect($lineup)
            ->filter(fn (array $slot): bool => ($slot['player_id'] ?? null) !== null && $slot['player_id'] !== $scorerId)
            ->sortByDesc(fn (array $slot): float => $slot['impact_score'])
            ->values();

        return $candidates->first();
    }

    private function weightedPick(array $items): array
    {
        $totalWeight = array_sum(array_map(static fn (array $item): float => (float) $item['weight'], $items));
        $cursor = random_int(1, max(1, (int) round($totalWeight * 1000))) / 1000;
        $sum = 0.0;

        foreach ($items as $item) {
            $sum += (float) $item['weight'];

            if ($cursor <= $sum) {
                return $item;
            }
        }

        return $items[array_key_last($items)];
    }

    private function selectSubstitutionOutIndex(array $lineup, bool $attackMode): int
    {
        $candidates = collect($lineup)
            ->map(function (array $slot, int $index) use ($attackMode): array {
                $fitnessPenalty = 100 - (int) ($slot['fitness'] ?? 50);
                $attackPenalty = $attackMode
                    ? match ($slot['role']) {
                        'FW' => 0,
                        'MF' => 5,
                        default => 9,
                    }
                    : match ($slot['role']) {
                        'DF' => 0,
                        'MF' => 4,
                        default => 7,
                    };

                return [
                    'index' => $index,
                    'score' => $fitnessPenalty + $attackPenalty + (50 - (float) $slot['impact_score']),
                ];
            })
            ->sortByDesc('score')
            ->values();

        return (int) ($candidates->first()['index'] ?? 0);
    }

    private function selectSubstitutionInIndex(array $bench, string $outRole, bool $attackMode): int
    {
        $candidates = collect($bench)
            ->map(function (array $slot, int $index) use ($outRole, $attackMode): array {
                $roleBonus = match ($outRole) {
                    'GK' => $slot['role'] === 'GK' ? 10 : 0,
                    'DF' => $slot['role'] === 'DF' ? 8 : ($slot['role'] === 'MF' ? 4 : 0),
                    'MF' => $slot['role'] === 'MF' ? 8 : ($slot['role'] === 'FW' ? 5 : 0),
                    default => $slot['role'] === 'FW' ? 8 : 2,
                };

                if ($attackMode) {
                    $roleBonus += match ($slot['role']) {
                        'FW' => 4,
                        'MF' => 2,
                        default => 0,
                    };
                }

                return [
                    'index' => $index,
                    'score' => ($slot['impact_score'] ?? 0) + $roleBonus + (($slot['fitness'] ?? 50) / 20),
                ];
            })
            ->sortByDesc('score')
            ->values();

        return (int) ($candidates->first()['index'] ?? 0);
    }

    private function formationSlots(string $formation): array
    {
        return match ($formation) {
            '4-4-2' => ['GK' => 1, 'DF' => 4, 'MF' => 4, 'FW' => 2],
            '3-5-2' => ['GK' => 1, 'DF' => 3, 'MF' => 5, 'FW' => 2],
            '3-4-3' => ['GK' => 1, 'DF' => 3, 'MF' => 4, 'FW' => 3],
            '4-2-3-1' => ['GK' => 1, 'DF' => 4, 'MF' => 5, 'FW' => 1],
            '5-3-2' => ['GK' => 1, 'DF' => 5, 'MF' => 3, 'FW' => 2],
            '4-1-4-1' => ['GK' => 1, 'DF' => 4, 'MF' => 5, 'FW' => 1],
            default => ['GK' => 1, 'DF' => 4, 'MF' => 3, 'FW' => 3],
        };
    }

    private function normalizeFormation(string $formation): string
    {
        return in_array($formation, ['4-3-3', '4-4-2', '3-5-2', '3-4-3', '4-2-3-1', '5-3-2', '4-1-4-1'], true)
            ? $formation
            : '4-3-3';
    }

    private function normalizeTactic(string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : $allowed[1];
    }

    private function normalizePosition(?string $position): string
    {
        $position = strtoupper((string) $position);

        return match (true) {
            str_starts_with($position, 'GK') => 'GK',
            str_starts_with($position, 'DF') => 'DF',
            str_starts_with($position, 'FW') => 'FW',
            default => 'MF',
        };
    }

    private function playerImpactScore($player): float
    {
        return (($player->overall ?? 50) * 0.85) + (($player->fitness ?? 50) * 0.15);
    }

    private function playerImpactValue(array $slot): float
    {
        return (float) ($slot['impact_score'] ?? 45);
    }

    private function pickBestPlayerForRole(Collection $players, array $usedIds, string $role)
    {
        $candidates = $players
            ->reject(fn ($player) => in_array($player->id, $usedIds, true))
            ->sortByDesc(fn ($player) => $this->roleScore($player, $role))
            ->values();

        $preferred = $candidates->first(fn ($player) => $this->normalizePosition($player->primary_position) === $role);

        return $preferred ?: $candidates->first();
    }

    private function roleScore($player, string $role): float
    {
        $position = $this->normalizePosition($player->primary_position);
        $fit = match ($role) {
            'GK' => $position === 'GK' ? 20 : 0,
            'DF' => $position === 'DF' ? 20 : ($position === 'MF' ? 6 : 0),
            'MF' => $position === 'MF' ? 20 : ($position === 'FW' ? 8 : 0),
            'FW' => $position === 'FW' ? 20 : ($position === 'MF' ? 7 : 0),
            default => 0,
        };

        return $this->playerImpactScore($player) + $fit;
    }

    private function formatPlayerSlot($player, string $role, bool $isStarting): array
    {
        return [
            'player_id' => $player->id,
            'name' => $player->display_name,
            'role' => $role,
            'primary_position' => $this->normalizePosition($player->primary_position),
            'overall' => (int) ($player->overall ?? 50),
            'fitness' => (int) ($player->fitness ?? 50),
            'impact_score' => round($this->playerImpactScore($player), 2),
            'is_starting' => $isStarting,
        ];
    }

    private function formatPlaceholderSlot(string $role): array
    {
        return [
            'player_id' => null,
            'name' => "Auto-Fill {$role}",
            'role' => $role,
            'primary_position' => $role,
            'overall' => 42,
            'fitness' => 100,
            'impact_score' => 42,
            'is_starting' => true,
        ];
    }

    private function calculatePossession(array $teamState, array $opponentState, bool $isHome): int
    {
        $attack = (float) $teamState['ratings']['attack'];
        $defense = (float) $opponentState['ratings']['defense'];

        $value = 50 + (($attack - $defense) / 3) + (($teamState['tactics']['tempo'] - 50) / 6) + (($teamState['tactics']['pressing'] - 50) / 8) + ($isHome ? 2 : -2);

        return max(35, min(65, (int) round($value)));
    }

    private function mentalityAttackModifier(string $mentality): float
    {
        return match ($mentality) {
            'attacking' => 0.22,
            'defensive' => -0.09,
            default => 0.05,
        };
    }

    private function mentalityDefenseModifier(string $mentality): float
    {
        return match ($mentality) {
            'defensive' => 0.22,
            'attacking' => -0.08,
            default => 0.08,
        };
    }

    private function clampStat(int $value): int
    {
        return max(0, min(100, $value));
    }

    private function poissonSample(float $lambda): int
    {
        $lambda = max(0.1, $lambda);
        $threshold = exp(-$lambda);
        $product = 1.0;
        $goals = 0;

        do {
            $goals++;
            $product *= random_int(1, 1000) / 1000;
        } while ($product > $threshold);

        return max(0, $goals - 1);
    }
}
