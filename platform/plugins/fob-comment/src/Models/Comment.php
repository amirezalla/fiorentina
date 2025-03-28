<?php

namespace FriendsOfBotble\Comment\Models;

use Botble\ACL\Contracts\HasPermissions;
use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Member\Models\Member;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Comment extends BaseModel
{
    use SoftDeletes;

    protected $table = 'fob_comments';

    protected $fillable = [
        'id',
        'name',
        'email',
        'content',
        'status',
        'author_id',
        'author_type',
        'reference_id',
        'reference_type',
        'reply_to',
        'ip_address',
        'user_agent',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'status' => CommentStatus::class,
    ];

    protected $dates = ['deleted_at'];

    /**
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Member::class,'likes')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function dislikes(): BelongsToMany
    {
        return $this->belongsToMany(Member::class,'dislikes')->withTimestamps();
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(static::class, 'reply_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(static::class, 'reply_to');
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->author && $this->author->avatar_url) {
                return $this->author->avatar_url;
            }

            $name = strtolower(trim($this->name));
            $hash = hash('sha256', $name);

            $default = urlencode("https://ui-avatars.com/api/?name=$name&background=441274&color=fff&size=32&font-size=0.33&?format=svg");

            return urldecode($default);
        });
    }

    protected function isApproved(): Attribute
    {
        return Attribute::get(fn () => $this->status == CommentStatus::APPROVED);
    }

    protected function isAdmin(): Attribute
    {
        return Attribute::get(
            fn () => $this->author && (
                $this->author instanceof HasPermissions
                && $this->author->hasPermission('fob-comment.comments.reply')
            )
        );
    }

    protected function formattedContent(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->is_admin) {
                return strip_tags($this->content);
            }

            return preg_replace('/<p[^>]*><\\/p[^>]*>/', '', $this->content);
        });
    }
}
