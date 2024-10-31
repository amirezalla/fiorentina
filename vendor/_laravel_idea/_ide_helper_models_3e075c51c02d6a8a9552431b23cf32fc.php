<?php //c084d1748186e13d3ac767ba935656c1
/** @noinspection all */

namespace ArchiElite\IpBlocker\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\ArchiElite\IpBlocker\Models\_IH_History_C;
    use LaravelIdea\Helper\ArchiElite\IpBlocker\Models\_IH_History_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;

    /**
     * @property int $id
     * @property string $ip_address
     * @property int $count_requests
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_History_QB onWriteConnection()
     * @method _IH_History_QB newQuery()
     * @method static _IH_History_QB on(null|string $connection = null)
     * @method static _IH_History_QB query()
     * @method static _IH_History_QB with(array|string $relations)
     * @method _IH_History_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_History_C|History[] all()
     * @mixin _IH_History_QB
     */
    class History extends Model {}
}
