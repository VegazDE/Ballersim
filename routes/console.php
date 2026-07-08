<?php

use App\Models\Club;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use App\Services\TeamProvisioningService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('baller:bootstrap-leagues {--teams-per-division=12}', function (): int {
    $teamsPerDivision = (int) $this->option('teams-per-division');
    $teamProvisioning = app(TeamProvisioningService::class);

    if ($teamsPerDivision < 2) {
        $this->error('teams-per-division must be at least 2.');

        return self::FAILURE;
    }

    if (Division::query()->exists()) {
        $this->warn('Leagues/Divisions already exist. Bootstrap runs only once.');

        return self::SUCCESS;
    }

    DB::transaction(function () use ($teamsPerDivision): void {
        foreach (range(1, 6) as $level) {
            $league = League::create([
                'name' => "Liga {$level}",
                'key' => "LIGA_{$level}",
                'level' => $level,
                'is_top_tier' => false,
            ]);

            foreach (['A', 'B'] as $index => $suffix) {
                $division = Division::create([
                    'league_id' => $league->id,
                    'name' => "Liga {$level} {$suffix}",
                    'code' => "L{$level}{$suffix}",
                    'position' => $index + 1,
                    'teams_target' => $teamsPerDivision,
                ]);

                foreach (range(1, $teamsPerDivision) as $slot) {
                    $clubCode = sprintf('L%s%s-%02d', $level, $suffix, $slot);

                    $club = Club::create([
                        'name' => "FC {$clubCode}",
                        'short_name' => $clubCode,
                        'budget' => 1000000,
                    ]);

                    $team = Team::create([
                        'club_id' => $club->id,
                        'league_id' => $league->id,
                        'division_id' => $division->id,
                        'manager_user_id' => null,
                        'name' => "Team {$clubCode}",
                        'is_cpu' => true,
                        'division_name' => $division->name,
                    ]);

                    $teamProvisioning->seedPlayersForTeam($team);
                }
            }
        }
    });

    $this->info('Bootstrap completed. Created 6 leagues, 12 divisions, and CPU teams.');

    return self::SUCCESS;
})->purpose('Create 6 leagues with 2 divisions each and auto-fill teams once.');

Artisan::command('baller:seed-team-players {--target=10}', function (): int {
    $target = max(6, (int) $this->option('target'));
    $teamProvisioning = app(TeamProvisioningService::class);
    $seededTeams = $teamProvisioning->seedPlayersForAllTeams($target);

    $this->info("Player seeding completed. Updated {$seededTeams} teams.");

    return self::SUCCESS;
})->purpose('Backfill players for teams with fewer than target roster size.');

Artisan::command('baller:generate-identities {--force-team-rename}', function (): int {
    $teamProvisioning = app(TeamProvisioningService::class);

    $seededTeams = $teamProvisioning->seedPlayersForAllTeams(10);
    $updatedTeams = $teamProvisioning->backfillTeamAndPlayerNames((bool) $this->option('force-team-rename'));

    $this->info("Identity generation done. Seeded players for {$seededTeams} teams, updated names for {$updatedTeams} teams.");

    return self::SUCCESS;
})->purpose('Generate or backfill team and player names for existing data.');

Artisan::command('baller:generate-season {name?} {--start-date=} {--matchday-interval-days=7}', function (): int {
    $divisionCount = Division::query()->count();

    if ($divisionCount === 0) {
        $this->error('No divisions found. Run baller:bootstrap-leagues first.');

        return self::FAILURE;
    }

    $seasonNumber = ((int) Season::query()->max('season_number')) + 1;
    $seasonName = (string) ($this->argument('name') ?: "Season {$seasonNumber}");
    $intervalDays = max(1, (int) $this->option('matchday-interval-days'));

    $startDateInput = $this->option('start-date');
    $startsAt = $startDateInput
        ? Carbon::parse((string) $startDateInput)->setTime(20, 0)
        : now()->addDay()->setTime(20, 0);

    $season = Season::create([
        'name' => $seasonName,
        'season_number' => $seasonNumber,
        'status' => Season::STATUS_PLANNED,
        'starts_at' => $startsAt,
        'match_duration_minutes' => (int) config('baller_manager.match_duration_minutes', 7),
    ]);

    $totalFixtures = 0;

    DB::transaction(function () use ($season, $startsAt, $intervalDays, &$totalFixtures): void {
        $divisions = Division::query()->with('teams:id,division_id,league_id')->orderBy('id')->get();

        foreach ($divisions as $division) {
            $teamIds = $division->teams->pluck('id')->values()->all();

            if (count($teamIds) < 2) {
                continue;
            }

            if (count($teamIds) % 2 !== 0) {
                $teamIds[] = null;
            }

            $rotation = $teamIds;
            $teamCount = count($rotation);
            $halfRounds = $teamCount - 1;
            $pairsPerRound = (int) ($teamCount / 2);

            for ($round = 0; $round < $halfRounds; $round++) {
                $firstLegKickoff = $startsAt->copy()->addDays($round * $intervalDays);
                $secondLegKickoff = $startsAt->copy()->addDays(($halfRounds + $round) * $intervalDays);

                for ($slot = 0; $slot < $pairsPerRound; $slot++) {
                    $homeId = $rotation[$slot];
                    $awayId = $rotation[$teamCount - 1 - $slot];

                    if ($homeId === null || $awayId === null) {
                        continue;
                    }

                    if ($round % 2 === 1) {
                        [$homeId, $awayId] = [$awayId, $homeId];
                    }

                    Fixture::create([
                        'season_id' => $season->id,
                        'league_id' => $division->league_id,
                        'division_id' => $division->id,
                        'matchday' => $round + 1,
                        'leg' => 1,
                        'home_team_id' => $homeId,
                        'away_team_id' => $awayId,
                        'kickoff_at' => $firstLegKickoff,
                        'status' => Fixture::STATUS_SCHEDULED,
                    ]);

                    Fixture::create([
                        'season_id' => $season->id,
                        'league_id' => $division->league_id,
                        'division_id' => $division->id,
                        'matchday' => $halfRounds + $round + 1,
                        'leg' => 2,
                        'home_team_id' => $awayId,
                        'away_team_id' => $homeId,
                        'kickoff_at' => $secondLegKickoff,
                        'status' => Fixture::STATUS_SCHEDULED,
                    ]);

                    $totalFixtures += 2;
                }

                $firstFixed = array_shift($rotation);
                $lastTeam = array_pop($rotation);
                array_unshift($rotation, $firstFixed, $lastTeam);
            }
        }
    });

    $this->info("Created {$season->name} with {$totalFixtures} fixtures.");

    return self::SUCCESS;
})->purpose('Generate a full home/away season schedule for all divisions.');
