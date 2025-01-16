<?php //a7143c084242fee47f1fdc89ef0c7b60
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\Blog\Models {

    use Botble\Blog\Models\Category;
    use Botble\Blog\Models\Post;
    use Botble\Blog\Models\Tag;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method Category|null getOrPut($key, $value)
     * @method Category|$this shift(int $count = 1)
     * @method Category|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Category|$this pop(int $count = 1)
     * @method Category|null pull($key, $default = null)
     * @method Category|null last(callable $callback = null, $default = null)
     * @method Category|$this random($number = null, bool $preserveKeys = false)
     * @method Category|null sole($key = null, $operator = null, $value = null)
     * @method Category|null get($key, $default = null)
     * @method Category|null first(callable $callback = null, $default = null)
     * @method Category|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Category|null find($key, $default = null)
     * @method Category[] all()
     */
    class _IH_Category_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Category[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Category_QB whereId($value)
     * @method _IH_Category_QB whereName($value)
     * @method _IH_Category_QB whereParentId($value)
     * @method _IH_Category_QB whereDescription($value)
     * @method _IH_Category_QB whereStatus($value)
     * @method _IH_Category_QB whereAuthorId($value)
     * @method _IH_Category_QB whereAuthorType($value)
     * @method _IH_Category_QB whereIcon($value)
     * @method _IH_Category_QB whereOrder($value)
     * @method _IH_Category_QB whereIsFeatured($value)
     * @method _IH_Category_QB whereIsDefault($value)
     * @method _IH_Category_QB whereCreatedAt($value)
     * @method _IH_Category_QB whereUpdatedAt($value)
     * @method Category baseSole(array|string $columns = ['*'])
     * @method Category create(array $attributes = [])
     * @method Category createOrFirst(array $attributes = [], array $values = [])
     * @method Category createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Category_C|Category[] cursor()
     * @method Category|null|_IH_Category_C|Category[] find($id, array|string $columns = ['*'])
     * @method _IH_Category_C|Category[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Category|_IH_Category_C|Category[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Category|_IH_Category_C|Category[] findOrFail($id, array|string $columns = ['*'])
     * @method Category|_IH_Category_C|Category[] findOrNew($id, array|string $columns = ['*'])
     * @method Category first(array|string $columns = ['*'])
     * @method Category firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Category firstOrCreate(array $attributes = [], array $values = [])
     * @method Category firstOrFail(array|string $columns = ['*'])
     * @method Category firstOrNew(array $attributes = [], array $values = [])
     * @method Category firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Category forceCreate(array $attributes)
     * @method Category forceCreateQuietly(array $attributes = [])
     * @method _IH_Category_C|Category[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Category_C|Category[] get(array|string $columns = ['*'])
     * @method Category getModel()
     * @method Category[] getModels(array|string $columns = ['*'])
     * @method _IH_Category_C|Category[] hydrate(array $items)
     * @method Category make(array $attributes = [])
     * @method Category newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Category[]|_IH_Category_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Category restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Category[]|_IH_Category_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Category sole(array|string $columns = ['*'])
     * @method Category updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Category_QB extends _BaseBuilder {}
    
    /**
     * @method Post|null getOrPut($key, $value)
     * @method Post|$this shift(int $count = 1)
     * @method Post|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Post|$this pop(int $count = 1)
     * @method Post|null pull($key, $default = null)
     * @method Post|null last(callable $callback = null, $default = null)
     * @method Post|$this random($number = null, bool $preserveKeys = false)
     * @method Post|null sole($key = null, $operator = null, $value = null)
     * @method Post|null get($key, $default = null)
     * @method Post|null first(callable $callback = null, $default = null)
     * @method Post|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Post|null find($key, $default = null)
     * @method Post[] all()
     */
    class _IH_Post_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Post[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Post_QB whereId($value)
     * @method _IH_Post_QB whereName($value)
     * @method _IH_Post_QB whereDescription($value)
     * @method _IH_Post_QB whereContent($value)
     * @method _IH_Post_QB whereStatus($value)
     * @method _IH_Post_QB whereAuthorId($value)
     * @method _IH_Post_QB whereAuthorType($value)
     * @method _IH_Post_QB whereIsFeatured($value)
     * @method _IH_Post_QB whereImage($value)
     * @method _IH_Post_QB whereViews($value)
     * @method _IH_Post_QB whereFormatType($value)
     * @method _IH_Post_QB whereCreatedAt($value)
     * @method _IH_Post_QB whereUpdatedAt($value)
     * @method Post baseSole(array|string $columns = ['*'])
     * @method Post create(array $attributes = [])
     * @method Post createOrFirst(array $attributes = [], array $values = [])
     * @method Post createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Post_C|Post[] cursor()
     * @method Post|null|_IH_Post_C|Post[] find($id, array|string $columns = ['*'])
     * @method _IH_Post_C|Post[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Post|_IH_Post_C|Post[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Post|_IH_Post_C|Post[] findOrFail($id, array|string $columns = ['*'])
     * @method Post|_IH_Post_C|Post[] findOrNew($id, array|string $columns = ['*'])
     * @method Post first(array|string $columns = ['*'])
     * @method Post firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Post firstOrCreate(array $attributes = [], array $values = [])
     * @method Post firstOrFail(array|string $columns = ['*'])
     * @method Post firstOrNew(array $attributes = [], array $values = [])
     * @method Post firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Post forceCreate(array $attributes)
     * @method Post forceCreateQuietly(array $attributes = [])
     * @method _IH_Post_C|Post[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Post_C|Post[] get(array|string $columns = ['*'])
     * @method Post getModel()
     * @method Post[] getModels(array|string $columns = ['*'])
     * @method _IH_Post_C|Post[] hydrate(array $items)
     * @method Post make(array $attributes = [])
     * @method Post newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Post[]|_IH_Post_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Post restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Post[]|_IH_Post_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Post sole(array|string $columns = ['*'])
     * @method Post updateOrCreate(array $attributes, array $values = [])
     * @method _IH_Post_QB published()
     * @method _IH_Post_QB unpublished()
     */
    class _IH_Post_QB extends _BaseBuilder {}
    
    /**
     * @method Tag|null getOrPut($key, $value)
     * @method Tag|$this shift(int $count = 1)
     * @method Tag|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Tag|$this pop(int $count = 1)
     * @method Tag|null pull($key, $default = null)
     * @method Tag|null last(callable $callback = null, $default = null)
     * @method Tag|$this random($number = null, bool $preserveKeys = false)
     * @method Tag|null sole($key = null, $operator = null, $value = null)
     * @method Tag|null get($key, $default = null)
     * @method Tag|null first(callable $callback = null, $default = null)
     * @method Tag|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Tag|null find($key, $default = null)
     * @method Tag[] all()
     */
    class _IH_Tag_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Tag[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Tag_QB whereId($value)
     * @method _IH_Tag_QB whereName($value)
     * @method _IH_Tag_QB whereAuthorId($value)
     * @method _IH_Tag_QB whereAuthorType($value)
     * @method _IH_Tag_QB whereDescription($value)
     * @method _IH_Tag_QB whereStatus($value)
     * @method _IH_Tag_QB whereCreatedAt($value)
     * @method _IH_Tag_QB whereUpdatedAt($value)
     * @method Tag baseSole(array|string $columns = ['*'])
     * @method Tag create(array $attributes = [])
     * @method Tag createOrFirst(array $attributes = [], array $values = [])
     * @method Tag createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Tag_C|Tag[] cursor()
     * @method Tag|null|_IH_Tag_C|Tag[] find($id, array|string $columns = ['*'])
     * @method _IH_Tag_C|Tag[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Tag|_IH_Tag_C|Tag[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Tag|_IH_Tag_C|Tag[] findOrFail($id, array|string $columns = ['*'])
     * @method Tag|_IH_Tag_C|Tag[] findOrNew($id, array|string $columns = ['*'])
     * @method Tag first(array|string $columns = ['*'])
     * @method Tag firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Tag firstOrCreate(array $attributes = [], array $values = [])
     * @method Tag firstOrFail(array|string $columns = ['*'])
     * @method Tag firstOrNew(array $attributes = [], array $values = [])
     * @method Tag firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Tag forceCreate(array $attributes)
     * @method Tag forceCreateQuietly(array $attributes = [])
     * @method _IH_Tag_C|Tag[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Tag_C|Tag[] get(array|string $columns = ['*'])
     * @method Tag getModel()
     * @method Tag[] getModels(array|string $columns = ['*'])
     * @method _IH_Tag_C|Tag[] hydrate(array $items)
     * @method Tag make(array $attributes = [])
     * @method Tag newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Tag[]|_IH_Tag_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Tag restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Tag[]|_IH_Tag_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Tag sole(array|string $columns = ['*'])
     * @method Tag updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Tag_QB extends _BaseBuilder {}
}