<?php

namespace App\Services;

use App\Models\Fixture;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MatchSimulationService
{
    public function simulateFixture(Fixture $fixture): Fixture
    {
        $fixture->loadMissing(['homeTeam.players', 'awayTeam.players']);

        $homeStrength = $this->calculateTeamStrength($fixture->homeTeam);
        $awayStrength = $this->calculateTeamStrength($fixture->awayTeam);

        $goalExpectation = max(0.4, abs($homeStrength - $awayStrength) / 12 + 1.1);
        $homeGoals = $this->generateGoals($homeStrength, $awayStrength, $goalExpectation, true);
        $awayGoals = $this->generateGoals($awayStrength, $homeStrength, $goalExpectation, false);

        if ($homeGoals === $awayGoals) {
            if (random_int(0, 1) === 1) {
                $homeGoals++;
            } else {
                $awayGoals++;
            }
        }

        $fixture->forceFill([
            'status' => Fixture::STATUS_FINISHED,
            'home_score' => $homeGoals,
            'away_score' => $awayGoals,
        ])->save();

        return $fixture->refresh();
    }

    public function simulatePendingFixtures(?int $seasonId = null, ?int $leagueId = null, ?int $divisionId = null, ?int $matchday = null, ?int $limit = null): int
    {
        $fixtures = Fixture::query()
            ->with(['homeTeam.players', 'awayTeam.players'])
            ->when($seasonId !== null, fn ($query) => $query->where('season_id', $seasonId))
            ->when($leagueId !== null, fn ($query) => $query->where('league_id', $leagueId))
            ->when($divisionId !== null, fn ($query) => $query->where('division_id', $divisionId))
            ->when($matchday !== null, fn ($query) => $query->where('matchday', $matchday))
            ->where('status', Fixture::STATUS_SCHEDULED)
            ->orderBy('kickoff_at')
            ->orderBy('matchday')
            ->orderBy('id');

        if ($limit !== null) {
            $fixtures->limit($limit);
        }

        return DB::transaction(function () use ($fixtures): int {
            $count = 0;

            foreach ($fixtures->get() as $fixture) {
                $this->simulateFixture($fixture);
                $count++;
            }

            return $count;
        });
    }

    private function calculateTeamStrength(?Team $team): float
    {
        if ($team === null) {
            return 50.0;
        }

        $players = $team->players
            ->sortByDesc('overall')
            ->take(11)
            ->values();

        if ($players->isEmpty()) {
            return 50.0;
        }

        $averageOverall = $players->avg('overall') ?? 50;
        $averageFitness = $players->avg('fitness') ?? 50;

        return ($averageOverall * 0.8) + ($averageFitness * 0.2);
    }

    private function generateGoals(float $forStrength, float $againstStrength, float $goalExpectation, bool $isHome): int
    {
        $strengthEdge = ($forStrength - $againstStrength) / 18;
        $venueEdge = $isHome ? 0.18 : -0.05;
        $base = max(0.2, $goalExpectation + $strengthEdge + $venueEdge);

        return $this->poissonSample($base);
    }

    private function poissonSample(float $lambda): int
    {
        $lambda = max(0.1, $lambda);
        $limit = exp(-$lambda);
        $product = 1.0;
        $goals = 0;

        do {
            $goals++;
            $product *= random_int(1, 1000) / 1000;
        } while ($product > $limit);

        return max(0, $goals - 1);
    }
}
