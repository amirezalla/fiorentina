<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdGroupImage extends Model
{
    protected $fillable = ['group_id', 'image_url', 'target_url'];

    public function group()
    {
        return $this->belongsTo(AdGroup::class, 'group_id');
    }
}
