<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamSettingsController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $team = $request->user()?->managedTeams()->firstOrFail();

        $validated = $request->validate([
            'formation' => ['required', 'string', Rule::in(['4-3-3', '4-4-2', '3-5-2', '3-4-3', '4-2-3-1', '5-3-2', '4-1-4-1'])],
            'mentality' => ['required', 'string', Rule::in(['defensive', 'balanced', 'attacking'])],
            'pressing' => ['required', 'integer', 'min:0', 'max:100'],
            'tempo' => ['required', 'integer', 'min:0', 'max:100'],
            'substitution_style' => ['required', 'integer', 'min:0', 'max:100'],
            'line_height' => ['required', 'integer', 'min:0', 'max:100'],
            'starting_lineup' => ['nullable', 'array', 'max:11'],
            'starting_lineup.*' => ['integer', 'exists:players,id'],
        ]);

        $playerIds = $request->user()?->managedTeams()
            ->whereKey($team->id)
            ->firstOrFail()
            ->players()
            ->pluck('id')
            ->all();

        $startingLineup = collect($validated['starting_lineup'] ?? [])
            ->filter(fn ($playerId) => in_array((int) $playerId, $playerIds, true))
            ->map(fn ($playerId) => (int) $playerId)
            ->unique()
            ->take(11)
            ->values()
            ->all();

        $team->update([
            'formation' => $validated['formation'],
            'mentality' => $validated['mentality'],
            'pressing' => $validated['pressing'],
            'tempo' => $validated['tempo'],
            'substitution_style' => $validated['substitution_style'],
            'line_height' => $validated['line_height'],
            'starting_lineup' => $startingLineup,
        ]);

        return back()->with('success', 'Taktik und Startelf gespeichert.');
    }
}
