<?php //283d03d61fdeb068d9f1548b0e4a3187
/** @noinspection all */

namespace Botble\Block\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Block\Models\_IH_Block_C;
    use LaravelIdea\Helper\Botble\Block\Models\_IH_Block_QB;

    /**
     * @property int $id
     * @property $name
     * @property string $alias
     * @property null $description
     * @property null $content
     * @property Enum $status
     * @property int|null $user_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_Block_QB onWriteConnection()
     * @method _IH_Block_QB newQuery()
     * @method static _IH_Block_QB on(null|string $connection = null)
     * @method static _IH_Block_QB query()
     * @method static _IH_Block_QB with(array|string $relations)
     * @method _IH_Block_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Block_C|Block[] all()
     * @ownLinks user_id,\App\Models\User,id
     * @foreignLinks
     * @mixin _IH_Block_QB
     */
    class Block extends Model {}
}
