<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $managerTeam = $request->user()?->managedTeams()
            ->with(['league:id,name,level', 'division:id,name'])
            ->orderBy('id')
            ->first(['id', 'name', 'league_id', 'division_id']);

        $defaultSeasonId = Season::query()->max('id');

        $seasonId = $request->integer('season_id') ?: $defaultSeasonId;
        $leagueId = $request->integer('league_id') ?: null;
        $divisionId = $request->integer('division_id') ?: null;
        $teamId = $request->integer('team_id') ?: null;
        $status = $request->string('status')->toString();

        $hasQueryFilters = collect($request->only(['season_id', 'league_id', 'division_id', 'team_id', 'status']))
            ->contains(static fn ($value): bool => $value !== null && $value !== '');

        if (! $hasQueryFilters && $managerTeam) {
            $teamId = $managerTeam->id;
            $leagueId = $managerTeam->league_id;
            $divisionId = $managerTeam->division_id;
        }

        if ($teamId !== null && ($leagueId === null || $divisionId === null)) {
            $team = Team::query()->find($teamId, ['id', 'league_id', 'division_id']);
            $leagueId ??= $team?->league_id;
            $divisionId ??= $team?->division_id;
        }

        $fixturesQuery = Fixture::query()
            ->with([
                'season:id,name,season_number',
                'league:id,name,level',
                'division:id,name,code',
                'homeTeam:id,name',
                'awayTeam:id,name',
            ])
            ->when($seasonId !== null, fn ($query) => $query->where('season_id', $seasonId))
            ->when($leagueId !== null, fn ($query) => $query->where('league_id', $leagueId))
            ->when($divisionId !== null, fn ($query) => $query->where('division_id', $divisionId))
            ->when($teamId !== null, fn ($query) => $query->where(static function ($inner) use ($teamId): void {
                $inner->where('home_team_id', $teamId)->orWhere('away_team_id', $teamId);
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('kickoff_at')
            ->orderBy('matchday')
            ->orderBy('id');

        $fixtures = $fixturesQuery
            ->limit(500)
            ->get()
            ->map(static fn ($fixture): array => [
                'id' => $fixture->id,
                'matchday' => $fixture->matchday,
                'leg' => $fixture->leg,
                'status' => $fixture->status,
                'kickoff_at' => optional($fixture->kickoff_at)?->toIso8601String(),
                'score' => $fixture->home_score !== null && $fixture->away_score !== null
                    ? "{$fixture->home_score}:{$fixture->away_score}"
                    : '-',
                'season' => [
                    'id' => $fixture->season?->id,
                    'name' => $fixture->season?->name,
                ],
                'league' => [
                    'id' => $fixture->league?->id,
                    'name' => $fixture->league?->name,
                ],
                'division' => [
                    'id' => $fixture->division?->id,
                    'name' => $fixture->division?->name,
                    'code' => $fixture->division?->code,
                ],
                'home_team' => [
                    'id' => $fixture->homeTeam?->id,
                    'name' => $fixture->homeTeam?->name,
                ],
                'away_team' => [
                    'id' => $fixture->awayTeam?->id,
                    'name' => $fixture->awayTeam?->name,
                ],
            ])
            ->values();

        $seasons = Season::query()
            ->orderByDesc('season_number')
            ->get(['id', 'name', 'season_number', 'status'])
            ->map(static fn ($season): array => [
                'id' => $season->id,
                'name' => $season->name,
                'season_number' => $season->season_number,
                'status' => $season->status,
            ])
            ->values();

        $leagues = League::query()
            ->with([
                'divisions' => fn ($divisionQuery) => $divisionQuery
                    ->orderBy('position')
                    ->select(['id', 'league_id', 'name', 'code', 'position'])
                    ->with([
                        'teams' => fn ($teamQuery) => $teamQuery
                            ->orderBy('name')
                            ->select(['id', 'name', 'league_id', 'division_id']),
                    ]),
            ])
            ->orderBy('level')
            ->get(['id', 'name', 'level'])
            ->map(static fn ($league): array => [
                'id' => $league->id,
                'name' => $league->name,
                'level' => $league->level,
                'divisions' => $league->divisions->map(static fn ($division): array => [
                    'id' => $division->id,
                    'name' => $division->name,
                    'code' => $division->code,
                    'position' => $division->position,
                    'teams' => $division->teams->map(static fn ($team): array => [
                        'id' => $team->id,
                        'name' => $team->name,
                        'league_id' => $team->league_id,
                        'division_id' => $team->division_id,
                    ])->values(),
                ])->values(),
            ])
            ->values();

        return Inertia::render('Schedule/Index', [
            'fixtures' => $fixtures,
            'filters' => [
                'season_id' => $seasonId,
                'league_id' => $leagueId,
                'division_id' => $divisionId,
                'team_id' => $teamId,
                'status' => $status !== '' ? $status : null,
            ],
            'options' => [
                'seasons' => $seasons,
                'leagues' => $leagues,
                'statuses' => [
                    Fixture::STATUS_SCHEDULED,
                    Fixture::STATUS_LIVE,
                    Fixture::STATUS_FINISHED,
                ],
            ],
            'default_team' => $managerTeam ? [
                'id' => $managerTeam->id,
                'name' => $managerTeam->name,
                'league_id' => $managerTeam->league_id,
                'division_id' => $managerTeam->division_id,
            ] : null,
        ]);
    }
}
