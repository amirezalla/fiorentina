<?php //62b95b24c6e6412aa11c2af2ba52623d
/** @noinspection all */

namespace Botble\Member\Models {

    use Botble\Base\Models\MetaBox;
    use Botble\Blog\Models\Post;
    use Botble\Media\Models\MediaFile;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Support\Carbon;
    use Laravel\Sanctum\PersonalAccessToken;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Post_C;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Post_QB;
    use LaravelIdea\Helper\Botble\Media\Models\_IH_MediaFile_QB;
    use LaravelIdea\Helper\Botble\Member\Models\_IH_MemberActivityLog_C;
    use LaravelIdea\Helper\Botble\Member\Models\_IH_MemberActivityLog_QB;
    use LaravelIdea\Helper\Botble\Member\Models\_IH_Member_C;
    use LaravelIdea\Helper\Botble\Member\Models\_IH_Member_QB;
    use LaravelIdea\Helper\Illuminate\Notifications\_IH_DatabaseNotification_QB;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_C;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_QB;

    /**
     * @property int $id
     * @property $first_name
     * @property $last_name
     * @property null $description
     * @property string|null $gender
     * @property string $email
     * @property mixed $password
     * @property int|null $avatar_id
     * @property Carbon|null $dob
     * @property string|null $phone
     * @property Carbon|null $confirmed_at
     * @property string|null $email_verify_token
     * @property string|null $remember_token
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string $status
     * @property-read null|string $avatar_thumb_url attribute
     * @property-read string $avatar_url attribute
     * @property-read string $name attribute
     * @property mixed $upload_folder attribute
     * @property MediaFile|null $avatar
     * @method BelongsTo|_IH_MediaFile_QB avatar()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $notifications
     * @property-read int $notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB notifications()
     * @property _IH_Post_C|Post[] $posts
     * @property-read int $posts_count
     * @method MorphToMany|_IH_Post_QB posts()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $readNotifications
     * @property-read int $read_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB readNotifications()
     * @property _IH_PersonalAccessToken_C|PersonalAccessToken[] $tokens
     * @property-read int $tokens_count
     * @method MorphToMany|_IH_PersonalAccessToken_QB tokens()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $unreadNotifications
     * @property-read int $unread_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB unreadNotifications()
     * @method static _IH_Member_QB onWriteConnection()
     * @method _IH_Member_QB newQuery()
     * @method static _IH_Member_QB on(null|string $connection = null)
     * @method static _IH_Member_QB query()
     * @method static _IH_Member_QB with(array|string $relations)
     * @method _IH_Member_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Member_C|Member[] all()
     * @foreignLinks id,\Botble\Member\Models\MemberActivityLog,member_id
     * @mixin _IH_Member_QB
     */
    class Member extends Model {}

    /**
     * @property int $id
     * @property $action
     * @property string|null $user_agent
     * @property string|null $reference_url
     * @property string|null $reference_name
     * @property string|null $ip_address
     * @property int $member_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_MemberActivityLog_QB onWriteConnection()
     * @method _IH_MemberActivityLog_QB newQuery()
     * @method static _IH_MemberActivityLog_QB on(null|string $connection = null)
     * @method static _IH_MemberActivityLog_QB query()
     * @method static _IH_MemberActivityLog_QB with(array|string $relations)
     * @method _IH_MemberActivityLog_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MemberActivityLog_C|MemberActivityLog[] all()
     * @ownLinks member_id,\Botble\Member\Models\Member,id
     * @mixin _IH_MemberActivityLog_QB
     */
    class MemberActivityLog extends Model {}
}
