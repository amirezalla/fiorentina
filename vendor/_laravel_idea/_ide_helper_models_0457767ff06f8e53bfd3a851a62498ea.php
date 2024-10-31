<?php //2a5056e13d30da94a5fe2863e077e378
/** @noinspection all */

namespace Botble\RequestLog\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\RequestLog\Models\_IH_RequestLog_C;
    use LaravelIdea\Helper\Botble\RequestLog\Models\_IH_RequestLog_QB;

    /**
     * @property int $id
     * @property int|null $status_code
     * @property string|null $url
     * @property int $count
     * @property array|null $user_id
     * @property array|null $referrer
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_RequestLog_QB onWriteConnection()
     * @method _IH_RequestLog_QB newQuery()
     * @method static _IH_RequestLog_QB on(null|string $connection = null)
     * @method static _IH_RequestLog_QB query()
     * @method static _IH_RequestLog_QB with(array|string $relations)
     * @method _IH_RequestLog_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_RequestLog_C|RequestLog[] all()
     * @ownLinks user_id,\App\Models\User,id
     * @mixin _IH_RequestLog_QB
     */
    class RequestLog extends Model {}
}
