<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Division;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TeamProvisioningService
{
    /** @var string[] */
    private array $teamPrefixes = [
        'FC', 'SV', 'AC', 'SC', 'VfL', 'Union', 'Athletik', 'TSV',
    ];

    /** @var string[] */
    private array $teamNouns = [
        'Falcons', 'Titans', 'Rangers', 'Strikers', 'Warriors', 'Comets', 'Lions', 'Phoenix',
        'Cyclones', 'Dragons', 'Wolves', 'Hawks',
    ];

    /** @var string[] */
    private array $cityNames = [
        'Berlin', 'Cologne', 'Hamburg', 'Munich', 'Dortmund', 'Leipzig', 'Bremen', 'Stuttgart',
        'Augsburg', 'Essen', 'Hannover', 'Kiel',
    ];

    /** @var string[] */
    private array $firstNames = [
        'Luca', 'Noah', 'Elias', 'Milan', 'Jonas', 'Felix', 'Ben', 'David', 'Leo', 'Nico',
        'Emil', 'Finn', 'Paul', 'Robin', 'Tim', 'Ruben', 'Mateo', 'Kai', 'Timo', 'Jan',
    ];

    /** @var string[] */
    private array $lastNames = [
        'Weber', 'Klein', 'Hoffmann', 'Schmidt', 'Meyer', 'Wagner', 'Braun', 'Kruger', 'Fischer', 'Lang',
        'Wolf', 'Schubert', 'Brandt', 'Otto', 'Peters', 'Hartmann', 'Jung', 'Franke', 'Becker', 'Dietz',
    ];

    /** @var string[] */
    private array $positionTemplate = ['GK', 'DF', 'DF', 'MF', 'MF', 'FW', 'GK', 'DF', 'MF', 'FW'];

    public function assignTeamToManager(User $user): Team
    {
        return DB::transaction(function () use ($user): Team {
            $team = Team::query()
                ->whereNull('manager_user_id')
                ->where('is_cpu', true)
                ->whereHas('division', fn ($query) => $query->where('code', 'like', 'L6%'))
                ->with(['division', 'league', 'club'])
                ->lockForUpdate()
                ->first();

            if (! $team) {
                $team = $this->createFallbackTeamForManager($user);
            }

            $team->update([
                'manager_user_id' => $user->id,
                'is_cpu' => false,
            ]);

            $this->seedPlayersForTeam($team);
            $this->backfillPlayerNamesForTeam($team);

            return $team->fresh(['club', 'league', 'division', 'players']);
        });
    }

    public function seedPlayersForTeam(Team $team, int $target = 10): void
    {
        $currentCount = $team->players()->count();

        if ($currentCount >= $target) {
            return;
        }

        for ($i = $currentCount; $i < $target; $i++) {
            $overall = random_int(45, 75);

            Player::create([
                'team_id' => $team->id,
                'first_name' => $this->firstNames[array_rand($this->firstNames)],
                'last_name' => $this->lastNames[array_rand($this->lastNames)],
                'primary_position' => $this->positionTemplate[$i] ?? 'MF',
                'overall' => $overall,
                'market_value' => ($overall * 10000) + random_int(5000, 60000),
                'fitness' => random_int(82, 100),
                'is_transfer_listed' => false,
            ]);
        }
    }

    public function seedPlayersForAllTeams(int $target = 10): int
    {
        $seededTeams = 0;

        Team::query()->withCount('players')->chunkById(100, function ($teams) use ($target, &$seededTeams): void {
            foreach ($teams as $team) {
                if ($team->players_count < $target) {
                    $this->seedPlayersForTeam($team, $target);
                    $seededTeams++;
                }
            }
        });

        return $seededTeams;
    }

    public function backfillTeamAndPlayerNames(bool $forceTeamRename = false): int
    {
        $updatedTeams = 0;

        Team::query()->with(['club', 'players'])->chunkById(100, function ($teams) use (&$updatedTeams, $forceTeamRename): void {
            foreach ($teams as $team) {
                $changed = false;

                if ($this->shouldRenameTeam($team, $forceTeamRename)) {
                    $identity = $this->generateTeamIdentity();

                    $team->update([
                        'name' => $identity['team_name'],
                    ]);

                    if ($team->club) {
                        $team->club->update([
                            'name' => $identity['club_name'],
                            'short_name' => $identity['short_name'],
                        ]);
                    }

                    $changed = true;
                }

                if ($this->backfillPlayerNamesForTeam($team) > 0) {
                    $changed = true;
                }

                if ($changed) {
                    $updatedTeams++;
                }
            }
        });

        return $updatedTeams;
    }

    public function backfillPlayerNamesForTeam(Team $team): int
    {
        $updatedPlayers = 0;

        foreach ($team->players as $player) {
            $firstName = trim((string) $player->first_name);
            $lastName = trim((string) $player->last_name);

            if ($firstName !== '' && $lastName !== '') {
                continue;
            }

            $player->update([
                'first_name' => $firstName !== '' ? $firstName : $this->firstNames[array_rand($this->firstNames)],
                'last_name' => $lastName !== '' ? $lastName : $this->lastNames[array_rand($this->lastNames)],
            ]);

            $updatedPlayers++;
        }

        return $updatedPlayers;
    }

    private function createFallbackTeamForManager(User $user): Team
    {
        $division = Division::query()
            ->whereIn('code', ['L6A', 'L6B'])
            ->orderBy('code')
            ->first();

        if (! $division) {
            throw new RuntimeException('No Liga 6 division found. Run baller:bootstrap-leagues first.');
        }

        $identity = $this->generateTeamIdentity();

        $club = Club::create([
            'name' => $identity['club_name'],
            'short_name' => $identity['short_name'],
            'budget' => 1000000,
        ]);

        return Team::create([
            'club_id' => $club->id,
            'league_id' => $division->league_id,
            'division_id' => $division->id,
            'manager_user_id' => null,
            'name' => $identity['team_name'],
            'is_cpu' => true,
            'division_name' => $division->name,
        ]);
    }

    private function shouldRenameTeam(Team $team, bool $forceTeamRename): bool
    {
        if ($forceTeamRename) {
            return true;
        }

        return (bool) preg_match('/^(Team\sL\d[AB]-\d{2}|Team\sManager\s\d+)$/', $team->name);
    }

    /**
     * @return array{team_name: string, club_name: string, short_name: string}
     */
    private function generateTeamIdentity(): array
    {
        $prefix = $this->teamPrefixes[array_rand($this->teamPrefixes)];
        $city = $this->cityNames[array_rand($this->cityNames)];
        $noun = $this->teamNouns[array_rand($this->teamNouns)];

        $teamName = "{$prefix} {$city} {$noun}";
        $clubName = "{$prefix} {$city}";
        $shortName = strtoupper(substr($prefix, 0, 1).substr($city, 0, 2).substr($noun, 0, 1));

        return [
            'team_name' => $teamName,
            'club_name' => $clubName,
            'short_name' => substr($shortName, 0, 12),
        ];
    }
}
