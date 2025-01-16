<?php //637b79fd3e276e587dc79b4090f3d10e
/** @noinspection all */

namespace FriendsOfBotble\Comment\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\ACL\Models\User;
    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\ACL\Models\_IH_User_C;
    use LaravelIdea\Helper\Botble\ACL\Models\_IH_User_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\FriendsOfBotble\Comment\Models\_IH_Comment_C;
    use LaravelIdea\Helper\FriendsOfBotble\Comment\Models\_IH_Comment_QB;
    
    /**
     * @property int $id
     * @property int|null $reply_to
     * @property int|null $author_id
     * @property string|null $author_type
     * @property int|null $reference_id
     * @property string|null $reference_type
     * @property string|null $reference_url
     * @property string $name
     * @property string|null $email
     * @property string|null $website
     * @property string $content
     * @property Enum $status
     * @property string $ip_address
     * @property string|null $user_agent
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read mixed|string $avatar_url attribute
     * @property-read array|null|string|string[] $formatted_content attribute
     * @property-read bool $is_admin attribute
     * @property-read bool $is_approved attribute
     * @property Model $author
     * @method MorphTo author()
     * @property Comment|null $comment
     * @method BelongsTo|_IH_Comment_QB comment()
     * @property _IH_User_C|User[] $dislikes
     * @property-read int $dislikes_count
     * @method BelongsToMany|_IH_User_QB dislikes()
     * @property _IH_User_C|User[] $likes
     * @property-read int $likes_count
     * @method BelongsToMany|_IH_User_QB likes()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Model $reference
     * @method MorphTo reference()
     * @property _IH_Comment_C|Comment[] $replies
     * @property-read int $replies_count
     * @method HasMany|_IH_Comment_QB replies()
     * @method static _IH_Comment_QB onWriteConnection()
     * @method _IH_Comment_QB newQuery()
     * @method static _IH_Comment_QB on(null|string $connection = null)
     * @method static _IH_Comment_QB query()
     * @method static _IH_Comment_QB with(array|string $relations)
     * @method _IH_Comment_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Comment_C|Comment[] all()
     * @mixin _IH_Comment_QB
     */
    class Comment extends Model {}
}