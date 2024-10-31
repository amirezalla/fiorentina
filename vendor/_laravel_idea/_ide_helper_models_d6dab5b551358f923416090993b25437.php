<?php //1d10db6c9c8d0df53d3d3c850bc5932c
/** @noinspection all */

namespace Botble\CustomField\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\Base\Models\MetaBox;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_CustomField_C;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_CustomField_QB;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_FieldGroup_C;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_FieldGroup_QB;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_FieldItem_C;
    use LaravelIdea\Helper\Botble\CustomField\Models\_IH_FieldItem_QB;

    /**
     * @property int $id
     * @property string $use_for
     * @property int $use_for_id
     * @property int $field_item_id
     * @property string $type
     * @property $slug
     * @property string|null $value
     * @property-read false|mixed $resolved_value attribute
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Model $useCustomFields
     * @method MorphTo useCustomFields()
     * @method static _IH_CustomField_QB onWriteConnection()
     * @method _IH_CustomField_QB newQuery()
     * @method static _IH_CustomField_QB on(null|string $connection = null)
     * @method static _IH_CustomField_QB query()
     * @method static _IH_CustomField_QB with(array|string $relations)
     * @method _IH_CustomField_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_CustomField_C|CustomField[] all()
     * @ownLinks field_item_id,\Botble\CustomField\Models\FieldItem,id
     * @foreignLinks
     * @mixin _IH_CustomField_QB
     */
    class CustomField extends Model {}

    /**
     * @property int $id
     * @property $title
     * @property string|null $rules
     * @property int $order
     * @property int|null $created_by
     * @property int|null $updated_by
     * @property Enum $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_FieldItem_C|FieldItem[] $fieldItems
     * @property-read int $field_items_count
     * @method HasMany|_IH_FieldItem_QB fieldItems()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_FieldGroup_QB onWriteConnection()
     * @method _IH_FieldGroup_QB newQuery()
     * @method static _IH_FieldGroup_QB on(null|string $connection = null)
     * @method static _IH_FieldGroup_QB query()
     * @method static _IH_FieldGroup_QB with(array|string $relations)
     * @method _IH_FieldGroup_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_FieldGroup_C|FieldGroup[] all()
     * @foreignLinks id,\Botble\CustomField\Models\FieldItem,field_group_id
     * @mixin _IH_FieldGroup_QB
     */
    class FieldGroup extends Model {}

    /**
     * @property int $id
     * @property int $field_group_id
     * @property int|null $parent_id
     * @property int|null $order
     * @property $title
     * @property string $slug
     * @property string $type
     * @property null $instructions
     * @property string|null $options
     * @property _IH_FieldItem_C|FieldItem[] $child
     * @property-read int $child_count
     * @method HasMany|_IH_FieldItem_QB child()
     * @property _IH_CustomField_C|CustomField[] $customFields
     * @property-read int $custom_fields_count
     * @method HasMany|_IH_CustomField_QB customFields()
     * @property FieldGroup $fieldGroup
     * @method BelongsTo|_IH_FieldGroup_QB fieldGroup()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property FieldItem|null $parent
     * @method BelongsTo|_IH_FieldItem_QB parent()
     * @method static _IH_FieldItem_QB onWriteConnection()
     * @method _IH_FieldItem_QB newQuery()
     * @method static _IH_FieldItem_QB on(null|string $connection = null)
     * @method static _IH_FieldItem_QB query()
     * @method static _IH_FieldItem_QB with(array|string $relations)
     * @method _IH_FieldItem_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_FieldItem_C|FieldItem[] all()
     * @ownLinks field_group_id,\Botble\CustomField\Models\FieldGroup,id
     * @foreignLinks id,\Botble\CustomField\Models\CustomField,field_item_id
     * @mixin _IH_FieldItem_QB
     */
    class FieldItem extends Model {}
}
