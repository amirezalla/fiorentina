<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormationVote extends Model
{
    protected $table = 'formation_votes';

    protected $fillable = [
        'match_id',     // from Calendario::match_id
        'team',         // e.g., 'fiorentina'
        'formation',    // e.g., '4-3-3'
        'positions',    // JSON: { "GK": 12, "DF1": 34, "DF2": 56, ... }
        'ip',
        'user_agent',
        'session_id',
    ];

    protected $casts = [
        'positions' => 'array',
    ];
}
