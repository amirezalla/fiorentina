<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchLineups extends Model
{
    protected $table = 'match_lineups';

    protected $fillable = [
        'match_id', 'formation_name', 'formation_disposition', 'player_id', 
        'player_full_name', 'player_position', 'player_number', 
        'player_country', 'player_rating', 'short_name', 'player_image'
    ];

    public function match()
    {
        return $this->belongsTo(Matches::class);
    }
}
