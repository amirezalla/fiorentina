<?php //3b03d9b89c47309ae75645337e028a72
/** @noinspection all */

namespace Botble\Blog\Models {

    use #Ф\Botble\Base\Supports\Enum;
    use Botble\Base\Models\MetaBox;
    use Botble\Revision\Revision;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Collection;
    use Illuminate\Support\HtmlString;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Category_C;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Category_QB;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Post_C;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Post_QB;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Tag_C;
    use LaravelIdea\Helper\Botble\Blog\Models\_IH_Tag_QB;
    use LaravelIdea\Helper\Botble\Revision\_IH_Revision_C;
    use LaravelIdea\Helper\Botble\Revision\_IH_Revision_QB;

    /**
     * @property int $id
     * @property $name
     * @property int $parent_id
     * @property null $description
     * @property Enum $status
     * @property int|null $author_id
     * @property string $author_type
     * @property string|null $icon
     * @property mixed $order
     * @property int $is_featured
     * @property bool $is_default
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read HtmlString $badge_with_count attribute
     * @property-read Collection $parents attribute
     * @property _IH_Category_C|Category[] $activeChildren
     * @property-read int $active_children_count
     * @method HasMany|_IH_Category_QB activeChildren()
     * @property _IH_Category_C|Category[] $children
     * @property-read int $children_count
     * @method HasMany|_IH_Category_QB children()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Category $parent
     * @method BelongsTo|_IH_Category_QB parent()
     * @property _IH_Post_C|Post[] $posts
     * @property-read int $posts_count
     * @method BelongsToMany|_IH_Post_QB posts()
     * @method static _IH_Category_QB onWriteConnection()
     * @method _IH_Category_QB newQuery()
     * @method static _IH_Category_QB on(null|string $connection = null)
     * @method static _IH_Category_QB query()
     * @method static _IH_Category_QB with(array|string $relations)
     * @method _IH_Category_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Category_C|Category[] all()
     * @foreignLinks
     * @mixin _IH_Category_QB
     */
    class Category extends Model {}

    /**
     * @property int $id
     * @property $name
     * @property null $description
     * @property string|null $content
     * @property Enum $status
     * @property int|null $author_id
     * @property string $author_type
     * @property int $is_featured
     * @property string|null $image
     * @property int $views
     * @property string|null $format_type
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read Category|null $first_category attribute
     * @property null|string $time_reading attribute
     * @property Model $author
     * @method MorphTo author()
     * @property _IH_Category_C|Category[] $categories
     * @property-read int $categories_count
     * @method BelongsToMany|_IH_Category_QB categories()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property _IH_Revision_C|Revision[] $revisionHistory
     * @property-read int $revision_history_count
     * @method MorphToMany|_IH_Revision_QB revisionHistory()
     * @property _IH_Tag_C|Tag[] $tags
     * @property-read int $tags_count
     * @method BelongsToMany|_IH_Tag_QB tags()
     * @method static _IH_Post_QB onWriteConnection()
     * @method _IH_Post_QB newQuery()
     * @method static _IH_Post_QB on(null|string $connection = null)
     * @method static _IH_Post_QB query()
     * @method static _IH_Post_QB with(array|string $relations)
     * @method _IH_Post_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Post_C|Post[] all()
     * @foreignLinks
     * @mixin _IH_Post_QB
     */
    class Post extends Model {}

    /**
     * @property int $id
     * @property $name
     * @property int|null $author_id
     * @property string $author_type
     * @property null $description
     * @property Enum $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property _IH_Post_C|Post[] $posts
     * @property-read int $posts_count
     * @method BelongsToMany|_IH_Post_QB posts()
     * @method static _IH_Tag_QB onWriteConnection()
     * @method _IH_Tag_QB newQuery()
     * @method static _IH_Tag_QB on(null|string $connection = null)
     * @method static _IH_Tag_QB query()
     * @method static _IH_Tag_QB with(array|string $relations)
     * @method _IH_Tag_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Tag_C|Tag[] all()
     * @foreignLinks
     * @mixin _IH_Tag_QB
     */
    class Tag extends Model {}
}
