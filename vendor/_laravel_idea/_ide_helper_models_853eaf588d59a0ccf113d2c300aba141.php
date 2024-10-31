<?php //d723b3d39eab7918519f58d890e0c48b
/** @noinspection all */

namespace Botble\Base\Models {

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_AdminNotification_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_BaseModel_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_BaseModel_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;

    /**
     * @property int $id
     * @property $title
     * @property null $action_label
     * @property null $action_url
     * @property null $description
     * @property Carbon|null $read_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string|null $permission
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static AdminNotificationQueryBuilder onWriteConnection()
     * @method AdminNotificationQueryBuilder newQuery()
     * @method static AdminNotificationQueryBuilder on(null|string $connection = null)
     * @method static AdminNotificationQueryBuilder query()
     * @method static AdminNotificationQueryBuilder with(array|string $relations)
     * @method AdminNotificationQueryBuilder newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AdminNotification_C|AdminNotification[] all()
     * @mixin AdminNotificationQueryBuilder
     */
    class AdminNotification extends Model {}

    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_BaseModel_QB onWriteConnection()
     * @method _IH_BaseModel_QB newQuery()
     * @method static _IH_BaseModel_QB on(null|string $connection = null)
     * @method static _IH_BaseModel_QB query()
     * @method static _IH_BaseModel_QB with(array|string $relations)
     * @method _IH_BaseModel_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_BaseModel_C|BaseModel[] all()
     * @mixin _IH_BaseModel_QB
     */
    class BaseModel extends Model {}

    /**
     * @property int $id
     * @property string $meta_key
     * @property array|null $meta_value
     * @property int $reference_id
     * @property string $reference_type
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Model $reference
     * @method MorphTo reference()
     * @method static _IH_MetaBox_QB onWriteConnection()
     * @method _IH_MetaBox_QB newQuery()
     * @method static _IH_MetaBox_QB on(null|string $connection = null)
     * @method static _IH_MetaBox_QB query()
     * @method static _IH_MetaBox_QB with(array|string $relations)
     * @method _IH_MetaBox_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MetaBox_C|MetaBox[] all()
     * @mixin _IH_MetaBox_QB
     */
    class MetaBox extends Model {}
}
