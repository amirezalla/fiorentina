<?php

namespace App\Models;

use Botble\Media\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;

class VideoSpec extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'video_spec';

    // The attributes that are mass assignable
    protected $fillable = [
        'video_id',
        'videolink',
        'external_link',
        'order',
    ];

    /**
     * Get the video that owns the VideoSpec.
     */
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
