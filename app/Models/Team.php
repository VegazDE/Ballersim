<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'manager_user_id',
        'league_id',
        'division_id',
        'name',
        'is_cpu',
        'division_name',
        'formation',
        'mentality',
        'pressing',
        'tempo',
        'substitution_style',
        'line_height',
    ];

    protected $casts = [
        'is_cpu' => 'boolean',
        'pressing' => 'integer',
        'tempo' => 'integer',
        'substitution_style' => 'integer',
        'line_height' => 'integer',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(TransferListing::class, 'seller_team_id');
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }
}
