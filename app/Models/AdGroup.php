<?php
// app/Models/AdGroup.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdGroup extends Model
{
    protected $fillable = ['name','slug','width','height','placement','status'];

    public function images() { return $this->hasMany(AdGroupImage::class, 'group_id'); }
    public function ads()    { return $this->hasMany(Ad::class, 'ad_group_id'); }
}
