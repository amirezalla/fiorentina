<?php //ee47e2d9cee3d8153eb87dfa2da174f3
/** @noinspection all */

namespace VigStudio\VigAutoTranslations\Http\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\VigStudio\VigAutoTranslations\Http\Models\_IH_VigTranslate_C;
    use LaravelIdea\Helper\VigStudio\VigAutoTranslations\Http\Models\_IH_VigTranslate_QB;

    /**
     * @property int $id
     * @property string|null $text_original
     * @property string|null $text_translated
     * @property string $lang_from
     * @property string $lang_to
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_VigTranslate_QB onWriteConnection()
     * @method _IH_VigTranslate_QB newQuery()
     * @method static _IH_VigTranslate_QB on(null|string $connection = null)
     * @method static _IH_VigTranslate_QB query()
     * @method static _IH_VigTranslate_QB with(array|string $relations)
     * @method _IH_VigTranslate_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_VigTranslate_C|VigTranslate[] all()
     * @mixin _IH_VigTranslate_QB
     */
    class VigTranslate extends Model {}
}
