<?php

namespace App\Models;

use Botble\Media\Models\MediaFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\Pure;

class Video extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    protected $casts = [
        'is_random' => 'bool',
        'published_at' => 'datetime',
    ];

    const PLAYLIST_MODE_SEQUENTIAL = "Sequential";
    const PLAYLIST_MODE_RANDOM = "Random";

    const PLAYLIST_MODES = [
        self::PLAYLIST_MODE_SEQUENTIAL,
        self::PLAYLIST_MODE_RANDOM,
    ];

    const STATUS_PUBLISHED = "Published";
    const STATUS_PENDING = "Pending";

    const STATUSES = [
        self::STATUS_PUBLISHED,
        self::STATUS_PENDING,
    ];

    /**
     * @return BelongsToMany
     */
    public function mediaFiles(): BelongsToMany
    {
        return $this->belongsToMany(MediaFile::class)->withPivot(['url','priority']);
    }

    /**
     * @return mixed
     */
    public function isRandom(): mixed
    {
        return $this->is_random;
    }

    /**
     * @return string
     */
    #[Pure] public function getModelLabel(): string
    {
        return $this->isRandom() ? self::PLAYLIST_MODE_RANDOM : self::PLAYLIST_MODE_SEQUENTIAL;
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return $this->published_at ? self::STATUS_PUBLISHED : self::STATUS_PENDING;
    }

    /**
     * @param $mode
     * @return bool
     */
    #[Pure] public function checkModelSelect($mode): bool
    {
        $result = false;

        if ($mode == self::PLAYLIST_MODE_RANDOM && $this->isRandom()){
            $result = true;
        }

        if ($mode == self::PLAYLIST_MODE_SEQUENTIAL && !$this->isRandom()){
            $result = true;
        }

        return $result;
    }

    /**
     * @param $status
     * @return bool
     */
    public function checkStatusSelect($status): bool
    {
        $result = false;

        if ($status == self::STATUS_PUBLISHED && $this->published_at){
            $result = true;
        }

        if ($status == self::STATUS_PENDING && is_null($this->published_at)){
            $result = true;
        }

        return $result;
    }

    public function scopePublished($q)
    {
        return $q->whereNotNull('published_at');
    }

    public function scopeOnlyForHome($q)
    {
        return $q->where('is_for_home',true);
    }

    public function scopeOnlyForPost($q)
    {
        return $q->where('is_for_post',true);
    }
}
