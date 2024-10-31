<?php //bc3c7ff52f7a679a91894b2256bf60f4
/** @noinspection all */

namespace Botble\LanguageAdvanced\Models {

    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\LanguageAdvanced\Models\_IH_TranslationResolver_C;
    use LaravelIdea\Helper\Botble\LanguageAdvanced\Models\_IH_TranslationResolver_QB;

    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_TranslationResolver_QB onWriteConnection()
     * @method _IH_TranslationResolver_QB newQuery()
     * @method static _IH_TranslationResolver_QB on(null|string $connection = null)
     * @method static _IH_TranslationResolver_QB query()
     * @method static _IH_TranslationResolver_QB with(array|string $relations)
     * @method _IH_TranslationResolver_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_TranslationResolver_C|TranslationResolver[] all()
     * @mixin _IH_TranslationResolver_QB
     */
    class TranslationResolver extends Model {}
}
