<?php //da004c34c896b66701b678b1fe81eb5f
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\Gallery\Models {

    use Botble\Gallery\Models\Gallery;
    use Botble\Gallery\Models\GalleryMeta;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method GalleryMeta|null getOrPut($key, $value)
     * @method GalleryMeta|$this shift(int $count = 1)
     * @method GalleryMeta|null firstOrFail($key = null, $operator = null, $value = null)
     * @method GalleryMeta|$this pop(int $count = 1)
     * @method GalleryMeta|null pull($key, $default = null)
     * @method GalleryMeta|null last(callable $callback = null, $default = null)
     * @method GalleryMeta|$this random($number = null, bool $preserveKeys = false)
     * @method GalleryMeta|null sole($key = null, $operator = null, $value = null)
     * @method GalleryMeta|null get($key, $default = null)
     * @method GalleryMeta|null first(callable $callback = null, $default = null)
     * @method GalleryMeta|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method GalleryMeta|null find($key, $default = null)
     * @method GalleryMeta[] all()
     */
    class _IH_GalleryMeta_C extends _BaseCollection {
        /**
         * @param int $size
         * @return GalleryMeta[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_GalleryMeta_QB whereId($value)
     * @method _IH_GalleryMeta_QB whereImages($value)
     * @method _IH_GalleryMeta_QB whereReferenceId($value)
     * @method _IH_GalleryMeta_QB whereReferenceType($value)
     * @method _IH_GalleryMeta_QB whereCreatedAt($value)
     * @method _IH_GalleryMeta_QB whereUpdatedAt($value)
     * @method GalleryMeta baseSole(array|string $columns = ['*'])
     * @method GalleryMeta create(array $attributes = [])
     * @method GalleryMeta createOrFirst(array $attributes = [], array $values = [])
     * @method GalleryMeta createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_GalleryMeta_C|GalleryMeta[] cursor()
     * @method GalleryMeta|null|_IH_GalleryMeta_C|GalleryMeta[] find($id, array|string $columns = ['*'])
     * @method _IH_GalleryMeta_C|GalleryMeta[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method GalleryMeta|_IH_GalleryMeta_C|GalleryMeta[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method GalleryMeta|_IH_GalleryMeta_C|GalleryMeta[] findOrFail($id, array|string $columns = ['*'])
     * @method GalleryMeta|_IH_GalleryMeta_C|GalleryMeta[] findOrNew($id, array|string $columns = ['*'])
     * @method GalleryMeta first(array|string $columns = ['*'])
     * @method GalleryMeta firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method GalleryMeta firstOrCreate(array $attributes = [], array $values = [])
     * @method GalleryMeta firstOrFail(array|string $columns = ['*'])
     * @method GalleryMeta firstOrNew(array $attributes = [], array $values = [])
     * @method GalleryMeta firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method GalleryMeta forceCreate(array $attributes)
     * @method GalleryMeta forceCreateQuietly(array $attributes = [])
     * @method _IH_GalleryMeta_C|GalleryMeta[] fromQuery(string $query, array $bindings = [])
     * @method _IH_GalleryMeta_C|GalleryMeta[] get(array|string $columns = ['*'])
     * @method GalleryMeta getModel()
     * @method GalleryMeta[] getModels(array|string $columns = ['*'])
     * @method _IH_GalleryMeta_C|GalleryMeta[] hydrate(array $items)
     * @method GalleryMeta make(array $attributes = [])
     * @method GalleryMeta newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|GalleryMeta[]|_IH_GalleryMeta_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method GalleryMeta restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|GalleryMeta[]|_IH_GalleryMeta_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method GalleryMeta sole(array|string $columns = ['*'])
     * @method GalleryMeta updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_GalleryMeta_QB extends _BaseBuilder {}
    
    /**
     * @method Gallery|null getOrPut($key, $value)
     * @method Gallery|$this shift(int $count = 1)
     * @method Gallery|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Gallery|$this pop(int $count = 1)
     * @method Gallery|null pull($key, $default = null)
     * @method Gallery|null last(callable $callback = null, $default = null)
     * @method Gallery|$this random($number = null, bool $preserveKeys = false)
     * @method Gallery|null sole($key = null, $operator = null, $value = null)
     * @method Gallery|null get($key, $default = null)
     * @method Gallery|null first(callable $callback = null, $default = null)
     * @method Gallery|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Gallery|null find($key, $default = null)
     * @method Gallery[] all()
     */
    class _IH_Gallery_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Gallery[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Gallery_QB whereId($value)
     * @method _IH_Gallery_QB whereName($value)
     * @method _IH_Gallery_QB whereDescription($value)
     * @method _IH_Gallery_QB whereIsFeatured($value)
     * @method _IH_Gallery_QB whereOrder($value)
     * @method _IH_Gallery_QB whereImage($value)
     * @method _IH_Gallery_QB whereUserId($value)
     * @method _IH_Gallery_QB whereStatus($value)
     * @method _IH_Gallery_QB whereCreatedAt($value)
     * @method _IH_Gallery_QB whereUpdatedAt($value)
     * @method Gallery baseSole(array|string $columns = ['*'])
     * @method Gallery create(array $attributes = [])
     * @method Gallery createOrFirst(array $attributes = [], array $values = [])
     * @method Gallery createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Gallery_C|Gallery[] cursor()
     * @method Gallery|null|_IH_Gallery_C|Gallery[] find($id, array|string $columns = ['*'])
     * @method _IH_Gallery_C|Gallery[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Gallery|_IH_Gallery_C|Gallery[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Gallery|_IH_Gallery_C|Gallery[] findOrFail($id, array|string $columns = ['*'])
     * @method Gallery|_IH_Gallery_C|Gallery[] findOrNew($id, array|string $columns = ['*'])
     * @method Gallery first(array|string $columns = ['*'])
     * @method Gallery firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Gallery firstOrCreate(array $attributes = [], array $values = [])
     * @method Gallery firstOrFail(array|string $columns = ['*'])
     * @method Gallery firstOrNew(array $attributes = [], array $values = [])
     * @method Gallery firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Gallery forceCreate(array $attributes)
     * @method Gallery forceCreateQuietly(array $attributes = [])
     * @method _IH_Gallery_C|Gallery[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Gallery_C|Gallery[] get(array|string $columns = ['*'])
     * @method Gallery getModel()
     * @method Gallery[] getModels(array|string $columns = ['*'])
     * @method _IH_Gallery_C|Gallery[] hydrate(array $items)
     * @method Gallery make(array $attributes = [])
     * @method Gallery newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Gallery[]|_IH_Gallery_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Gallery restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Gallery[]|_IH_Gallery_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Gallery sole(array|string $columns = ['*'])
     * @method Gallery updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Gallery_QB extends _BaseBuilder {}
}