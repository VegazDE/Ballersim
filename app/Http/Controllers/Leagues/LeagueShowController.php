<?php

namespace App\Http\Controllers\Leagues;

use App\Http\Controllers\Controller;
use App\Models\League;
use Inertia\Inertia;
use Inertia\Response;

class LeagueShowController extends Controller
{
    public function __invoke(League $league): Response
    {
        $league->load([
            'divisions' => function ($query): void {
                $query->withCount('teams')->orderBy('position');
            },
        ])->loadCount('teams');

        return Inertia::render('Leagues/Show', [
            'league' => [
                'id' => $league->id,
                'name' => $league->name,
                'level' => $league->level,
                'teams_count' => $league->teams_count,
                'divisions' => $league->divisions->map(static fn ($division): array => [
                    'id' => $division->id,
                    'name' => $division->name,
                    'code' => $division->code,
                    'teams_count' => $division->teams_count,
                    'teams_target' => $division->teams_target,
                ])->values(),
            ],
        ]);
    }
}
