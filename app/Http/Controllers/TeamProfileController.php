<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamProfileController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $team = $request->user()?->managedTeams()
            ->with([
                'club',
                'league',
                'division',
                'players' => fn ($query) => $query->orderByDesc('overall')->orderBy('primary_position'),
            ])
            ->first();

        if (! $team) {
            return Inertia::render('Team/Profile', [
                'team' => null,
                'players' => [],
            ]);
        }

        return Inertia::render('Team/Profile', [
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'club' => $team->club?->name,
                'league' => $team->league?->name,
                'division' => $team->division?->name,
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
