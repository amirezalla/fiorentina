<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

use Botble\Member\Models\Member;

class Message extends Model
{
    protected $fillable = ['user_id', 'message', 'match_id'];
    use SoftDeletes;   // enable soft‑deletes
    // Define the relationship with the match
    public function match()
    {
        return $this->belongsTo(Calendario::class);
    }

    // Define the relationship with the user
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
