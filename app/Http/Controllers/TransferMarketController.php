<?php

namespace App\Http\Controllers;

use App\Models\TransferListing;
use Inertia\Inertia;
use Inertia\Response;

class TransferMarketController extends Controller
{
    public function __invoke(): Response
    {
        $listings = TransferListing::query()
            ->with(['player', 'sellerTeam'])
            ->where('status', TransferListing::STATUS_OPEN)
            ->orderByDesc('listed_at')
            ->limit(50)
            ->get()
            ->map(static function (TransferListing $listing): array {
                return [
                    'id' => $listing->id,
                    'player' => [
                        'name' => $listing->player?->display_name,
                        'position' => $listing->player?->primary_position,
                        'overall' => $listing->player?->overall,
                    ],
                    'seller_team' => $listing->sellerTeam?->name,
                    'asking_price' => (int) $listing->asking_price,
                    'minimum_bid' => $listing->minimum_bid ? (int) $listing->minimum_bid : null,
                    'expires_at' => optional($listing->expires_at)?->toIso8601String(),
                ];
            });

        return Inertia::render('TransferMarket/Index', [
            'listings' => $listings,
        ]);
    }
}
