<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdLabel extends Model
{
    protected $table = 'ad_labels';
    protected $fillable = ['name'];
}
