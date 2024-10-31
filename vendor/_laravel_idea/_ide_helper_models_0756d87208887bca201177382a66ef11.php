<?php //0ff8b88d87475be10abf31a03b1efff2
/** @noinspection all */

namespace Botble\Contact\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Contact\Models\_IH_ContactReply_C;
    use LaravelIdea\Helper\Botble\Contact\Models\_IH_ContactReply_QB;
    use LaravelIdea\Helper\Botble\Contact\Models\_IH_Contact_C;
    use LaravelIdea\Helper\Botble\Contact\Models\_IH_Contact_QB;

    /**
     * @property int $id
     * @property $name
     * @property string|null $email
     * @property string|null $phone
     * @property null $address
     * @property null $subject
     * @property $content
     * @property Enum $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read string $avatar_url attribute
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property _IH_ContactReply_C|ContactReply[] $replies
     * @property-read int $replies_count
     * @method HasMany|_IH_ContactReply_QB replies()
     * @method static _IH_Contact_QB onWriteConnection()
     * @method _IH_Contact_QB newQuery()
     * @method static _IH_Contact_QB on(null|string $connection = null)
     * @method static _IH_Contact_QB query()
     * @method static _IH_Contact_QB with(array|string $relations)
     * @method _IH_Contact_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Contact_C|Contact[] all()
     * @foreignLinks id,\Botble\Contact\Models\ContactReply,contact_id
     * @mixin _IH_Contact_QB
     */
    class Contact extends Model {}

    /**
     * @property int $id
     * @property $message
     * @property int $contact_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_ContactReply_QB onWriteConnection()
     * @method _IH_ContactReply_QB newQuery()
     * @method static _IH_ContactReply_QB on(null|string $connection = null)
     * @method static _IH_ContactReply_QB query()
     * @method static _IH_ContactReply_QB with(array|string $relations)
     * @method _IH_ContactReply_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_ContactReply_C|ContactReply[] all()
     * @ownLinks contact_id,\Botble\Contact\Models\Contact,id
     * @mixin _IH_ContactReply_QB
     */
    class ContactReply extends Model {}
}
