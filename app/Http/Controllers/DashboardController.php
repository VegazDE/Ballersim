<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $team = $request->user()?->managedTeams()
            ->with(['club', 'league', 'division'])
            ->first();

        return Inertia::render('Dashboard/Index', [
            'team' => $team ? [
                'id' => $team->id,
                'name' => $team->name,
                'club' => $team->club?->name,
                'league' => $team->league?->name,
                'division' => $team->division?->name,
            ] : null,
        ]);
    }
}
