<?php

namespace App\Http\Controllers\Leagues;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\League;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DivisionShowController extends Controller
{
    public function __invoke(League $league, Division $division): Response
    {
        abort_if($division->league_id !== $league->id, 404);

        $division->load([
            'teams' => fn ($query) => $query->with('club')->orderBy('name'),
            'fixtures' => fn ($query) => $query
                ->with(['homeTeam', 'awayTeam', 'season'])
                ->latest('matchday')
                ->limit(60),
        ]);

        $standings = $this->buildStandings($division);

        $fixtures = $division->fixtures
            ->sortBy([
                ['matchday', 'asc'],
                ['id', 'asc'],
            ])
            ->map(static fn ($fixture): array => [
                'id' => $fixture->id,
                'matchday' => $fixture->matchday,
                'status' => $fixture->status,
                'season' => $fixture->season?->name,
                'home_team' => [
                    'id' => $fixture->homeTeam?->id,
                    'name' => $fixture->homeTeam?->name,
                ],
                'away_team' => [
                    'id' => $fixture->awayTeam?->id,
                    'name' => $fixture->awayTeam?->name,
                ],
                'score' => $fixture->home_score !== null && $fixture->away_score !== null
                    ? "{$fixture->home_score}:{$fixture->away_score}"
                    : '-',
                'kickoff_at' => optional($fixture->kickoff_at)?->toIso8601String(),
            ])->values();

        return Inertia::render('Divisions/Show', [
            'league' => [
                'id' => $league->id,
                'name' => $league->name,
                'level' => $league->level,
            ],
            'division' => [
                'id' => $division->id,
                'name' => $division->name,
                'code' => $division->code,
                'teams_target' => $division->teams_target,
            ],
            'standings' => $standings,
            'fixtures' => $fixtures,
        ]);
    }

    private function buildStandings(Division $division): array
    {
        $rows = $division->teams->mapWithKeys(static fn ($team): array => [
            $team->id => [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'club_name' => $team->club?->name,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_diff' => 0,
                'points' => 0,
            ],
        ]);

        $finishedFixtures = $division->fixtures
            ->where('status', 'finished')
            ->filter(static fn ($fixture) => $fixture->home_score !== null && $fixture->away_score !== null);

        foreach ($finishedFixtures as $fixture) {
            $homeId = $fixture->home_team_id;
            $awayId = $fixture->away_team_id;

            if (! isset($rows[$homeId], $rows[$awayId])) {
                continue;
            }

            $homeGoals = (int) $fixture->home_score;
            $awayGoals = (int) $fixture->away_score;

            $rows[$homeId]['played']++;
            $rows[$awayId]['played']++;

            $rows[$homeId]['goals_for'] += $homeGoals;
            $rows[$homeId]['goals_against'] += $awayGoals;
            $rows[$awayId]['goals_for'] += $awayGoals;
            $rows[$awayId]['goals_against'] += $homeGoals;

            if ($homeGoals > $awayGoals) {
                $rows[$homeId]['wins']++;
                $rows[$homeId]['points'] += 3;
                $rows[$awayId]['losses']++;
            } elseif ($awayGoals > $homeGoals) {
                $rows[$awayId]['wins']++;
                $rows[$awayId]['points'] += 3;
                $rows[$homeId]['losses']++;
            } else {
                $rows[$homeId]['draws']++;
                $rows[$awayId]['draws']++;
                $rows[$homeId]['points']++;
                $rows[$awayId]['points']++;
            }
        }

        return Collection::make($rows)
            ->map(static function (array $row): array {
                $row['goal_diff'] = $row['goals_for'] - $row['goals_against'];

                return $row;
            })
            ->sortBy([
                ['points', 'desc'],
                ['goal_diff', 'desc'],
                ['goals_for', 'desc'],
                ['team_name', 'asc'],
            ])
            ->values()
            ->map(static function (array $row, int $index): array {
                $row['position'] = $index + 1;

                return $row;
            })
            ->all();
    }
}
