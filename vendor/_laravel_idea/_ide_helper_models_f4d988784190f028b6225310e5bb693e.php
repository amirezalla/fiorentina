<?php //3d28b1086d38581b3ccda5695443e67f
/** @noinspection all */

namespace Botble\Api\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Api\Models\_IH_PersonalAccessToken_C;
    use LaravelIdea\Helper\Botble\Api\Models\_IH_PersonalAccessToken_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;

    /**
     * @property int $id
     * @property int $tokenable_id
     * @property string $tokenable_type
     * @property string $name
     * @property string $token
     * @property array|null $abilities
     * @property Carbon|null $last_used_at
     * @property Carbon|null $expires_at
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Model $tokenable
     * @method MorphTo tokenable()
     * @method static _IH_PersonalAccessToken_QB onWriteConnection()
     * @method _IH_PersonalAccessToken_QB newQuery()
     * @method static _IH_PersonalAccessToken_QB on(null|string $connection = null)
     * @method static _IH_PersonalAccessToken_QB query()
     * @method static _IH_PersonalAccessToken_QB with(array|string $relations)
     * @method _IH_PersonalAccessToken_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PersonalAccessToken_C|PersonalAccessToken[] all()
     * @mixin _IH_PersonalAccessToken_QB
     */
    class PersonalAccessToken extends Model {}
}
