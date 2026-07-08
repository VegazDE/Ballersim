<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferListing extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_SOLD = 'sold';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'player_id',
        'seller_team_id',
        'asking_price',
        'minimum_bid',
        'listed_at',
        'expires_at',
        'status',
        'sold_to_team_id',
        'sold_price',
        'closed_at',
    ];

    protected $casts = [
        'listed_at' => 'datetime',
        'expires_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function sellerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'seller_team_id');
    }

    public function soldToTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'sold_to_team_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(TransferBid::class);
    }
}
