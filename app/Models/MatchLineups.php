<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchLineups extends Model
{
    protected $table = 'match_lineups';

    protected $fillable = [
        'match_id', 'formation_name', 'formation_disposition', 'player_id',
        'player_full_name', 'player_position', 'player_number',
        'player_country', 'player_rating', 'short_name', 'player_image'
    ];

    public function getAverageRate(): float
    {
        return round(Poll::query()
            ->whereHas('matchLineup', function ($q) {
                $q->where('player_full_name', $this->player_full_name);
            })
            ->avg('polls.value'), 2);
    }

    public function getMaxRate(): int
    {
        return 10;
    }

    public function match()
    {
        return $this->belongsTo(Matches::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }
}
