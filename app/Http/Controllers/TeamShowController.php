<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class TeamShowController extends Controller
{
    public function __invoke(Team $team): Response
    {
        $team->load([
            'club',
            'league',
            'division',
            'players' => fn ($query) => $query->orderByDesc('overall')->orderBy('primary_position'),
        ]);

        return Inertia::render('Teams/Show', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'club' => $team->club?->name,
                'league' => $team->league?->name,
                'division' => $team->division?->name,
                'is_cpu' => $team->is_cpu,
                'budget' => $team->club?->budget,
            ],
            'players' => $team->players->map(static fn ($player): array => [
                'id' => $player->id,
                'name' => $player->display_name,
                'position' => $player->primary_position,
                'overall' => $player->overall,
                'fitness' => $player->fitness,
                'market_value' => $player->market_value,
            ])->values(),
        ]);
    }
}
