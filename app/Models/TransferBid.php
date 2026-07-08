<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferBid extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'transfer_listing_id',
        'bidder_team_id',
        'bid_amount',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(TransferListing::class, 'transfer_listing_id');
    }

    public function bidderTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'bidder_team_id');
    }
}
