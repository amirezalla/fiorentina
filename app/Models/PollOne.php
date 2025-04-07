<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOne extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'active', 'min_choices'];

    /**
     * Relationship: A poll has many options
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Scope to retrieve only active polls
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
