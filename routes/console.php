<?php

use App\Models\Club;
use App\Models\Division;
use App\Models\League;
use App\Models\Team;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('baller:bootstrap-leagues {--teams-per-division=12}', function (): int {
    $teamsPerDivision = (int) $this->option('teams-per-division');

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

                    Team::create([
                        'club_id' => $club->id,
                        'league_id' => $league->id,
                        'division_id' => $division->id,
                        'manager_user_id' => null,
                        'name' => "Team {$clubCode}",
                        'is_cpu' => true,
                        'division_name' => $division->name,
                    ]);
                }
            }
        }
    });

    $this->info('Bootstrap completed. Created 6 leagues, 12 divisions, and CPU teams.');

    return self::SUCCESS;
})->purpose('Create 6 leagues with 2 divisions each and auto-fill teams once.');
