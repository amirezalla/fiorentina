<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';

    protected $fillable = [
        'name','league','position','season','image','jersey_number','flag_id'
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    public function getImageUrl($playername){

        $playerImage=Player::where('name',$playername)->first();
        return $playerImage->image;

    }

    public function wasabiImage($playername){
        $playerImage=Player::where('name',$playername)->first();
        return Storage::temporaryUrl($playerImage->image, now()->addMinutes(20));
    }
}
