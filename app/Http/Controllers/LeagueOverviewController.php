<?php

namespace App\Http\Controllers;

use App\Models\League;
use Inertia\Inertia;
use Inertia\Response;

class LeagueOverviewController extends Controller
{
    public function __invoke(): Response
    {
        $leagues = League::query()
            ->with(['divisions' => function ($query): void {
                $query->withCount('teams')->orderBy('position');
            }])
            ->withCount('teams')
            ->orderBy('level')
            ->get()
            ->map(static function (League $league): array {
                return [
                    'id' => $league->id,
                    'name' => $league->name,
                    'level' => $league->level,
                    'teams_count' => $league->teams_count,
                    'divisions' => $league->divisions->map(static fn ($division): array => [
                        'id' => $division->id,
                        'name' => $division->name,
                        'code' => $division->code,
                        'teams_target' => $division->teams_target,
                        'teams_count' => $division->teams_count,
                    ])->values(),
                ];
            });

        return Inertia::render('Leagues/Index', [
            'leagues' => $leagues,
        ]);
    }
}
