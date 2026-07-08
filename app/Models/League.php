<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'level',
        'is_top_tier',
    ];

    protected $casts = [
        'is_top_tier' => 'boolean',
    ];

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
