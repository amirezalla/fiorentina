<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    use HasFactory;

    protected $table = 'calendario';

    protected $fillable = [
        'match_id', 'venue', 'matchday', 'competition', 'group', 'match_date',
        'status', 'home_team', 'away_team', 'score', 'goals', 'penalties',
        'bookings', 'substitutions', 'odds', 'referees'
    ];

    protected $casts = [
        'home_team' => 'array',
        'away_team' => 'array',
        'score' => 'array',
        'goals' => 'array',
        'penalties' => 'array',
        'bookings' => 'array',
        'substitutions' => 'array',
        'odds' => 'array',
        'referees' => 'array'
    ];

    public function comments()
    {
        return $this->hasMany(DirettaComment::class);
    }
    public function votes()
    {
        return $this->hasMany(Vote::class, 'match_id', 'id');
    }
}
