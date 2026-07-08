<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlayerProfileController extends Controller
{
    public function __invoke(Request $request, Player $player): Response
    {
        $managerTeam = $request->user()?->managedTeams()->first();

        abort_if(! $managerTeam, 404);
        abort_if($player->team_id !== $managerTeam->id, 403);

        $player->load(['team.club', 'team.league', 'team.division']);

        return Inertia::render('Player/Profile', [
            'player' => [
                'id' => $player->id,
                'name' => $player->display_name,
                'first_name' => $player->first_name,
                'last_name' => $player->last_name,
                'position' => $player->primary_position,
                'overall' => $player->overall,
                'fitness' => $player->fitness,
                'market_value' => $player->market_value,
                'team' => [
                    'id' => $player->team?->id,
                    'name' => $player->team?->name,
                    'club' => $player->team?->club?->name,
                    'league' => $player->team?->league?->name,
                    'division' => $player->team?->division?->name,
                ],
            ],
        ]);
    }
}
