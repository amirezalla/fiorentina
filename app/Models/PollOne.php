<?php

namespace App\Models;

use Botble\Member\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollOne extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'active'];
    public function options()
    {
        return $this->hasMany(PollOption::class);
    }
}