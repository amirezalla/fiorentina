<?php //2d828fd6788a9b2de19eb732404d371a
/** @noinspection all */

namespace Botble\Gallery\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\ACL\Models\User;
    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\ACL\Models\_IH_User_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Gallery\Models\_IH_GalleryMeta_C;
    use LaravelIdea\Helper\Botble\Gallery\Models\_IH_GalleryMeta_QB;
    use LaravelIdea\Helper\Botble\Gallery\Models\_IH_Gallery_C;
    use LaravelIdea\Helper\Botble\Gallery\Models\_IH_Gallery_QB;

    /**
     * @property int $id
     * @property $name
     * @property $description
     * @property int $is_featured
     * @property int $order
     * @property string|null $image
     * @property int|null $user_id
     * @property Enum $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property User|null $user
     * @method BelongsTo|_IH_User_QB user()
     * @method static _IH_Gallery_QB onWriteConnection()
     * @method _IH_Gallery_QB newQuery()
     * @method static _IH_Gallery_QB on(null|string $connection = null)
     * @method static _IH_Gallery_QB query()
     * @method static _IH_Gallery_QB with(array|string $relations)
     * @method _IH_Gallery_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Gallery_C|Gallery[] all()
     * @ownLinks user_id,\App\Models\User,id
     * @foreignLinks
     * @mixin _IH_Gallery_QB
     */
    class Gallery extends Model {}

    /**
     * @property int $id
     * @property array|null $images
     * @property int $reference_id
     * @property string $reference_type
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_GalleryMeta_QB onWriteConnection()
     * @method _IH_GalleryMeta_QB newQuery()
     * @method static _IH_GalleryMeta_QB on(null|string $connection = null)
     * @method static _IH_GalleryMeta_QB query()
     * @method static _IH_GalleryMeta_QB with(array|string $relations)
     * @method _IH_GalleryMeta_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_GalleryMeta_C|GalleryMeta[] all()
     * @foreignLinks
     * @mixin _IH_GalleryMeta_QB
     */
    class GalleryMeta extends Model {}
}
