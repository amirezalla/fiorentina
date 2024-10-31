<?php //4640770fcd31237bcb5ac087cf6f05e5
/** @noinspection all */

namespace VigStudio\VigSeo\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\VigStudio\VigSeo\Models\_IH_VigSeoTranslation_C;
    use LaravelIdea\Helper\VigStudio\VigSeo\Models\_IH_VigSeoTranslation_QB;
    use LaravelIdea\Helper\VigStudio\VigSeo\Models\_IH_VigSeo_C;
    use LaravelIdea\Helper\VigStudio\VigSeo\Models\_IH_VigSeo_QB;

    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_VigSeo_QB onWriteConnection()
     * @method _IH_VigSeo_QB newQuery()
     * @method static _IH_VigSeo_QB on(null|string $connection = null)
     * @method static _IH_VigSeo_QB query()
     * @method static _IH_VigSeo_QB with(array|string $relations)
     * @method _IH_VigSeo_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_VigSeo_C|VigSeo[] all()
     * @mixin _IH_VigSeo_QB
     */
    class VigSeo extends Model {}

    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_VigSeoTranslation_QB onWriteConnection()
     * @method _IH_VigSeoTranslation_QB newQuery()
     * @method static _IH_VigSeoTranslation_QB on(null|string $connection = null)
     * @method static _IH_VigSeoTranslation_QB query()
     * @method static _IH_VigSeoTranslation_QB with(array|string $relations)
     * @method _IH_VigSeoTranslation_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_VigSeoTranslation_C|VigSeoTranslation[] all()
     * @mixin _IH_VigSeoTranslation_QB
     */
    class VigSeoTranslation extends Model {}
}
