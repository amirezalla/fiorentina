<?php //55368c1e3f0267f3242f9ed64f31b938
/** @noinspection all */

namespace LaravelIdea\Helper\VigStudio\VigSeo\Models {

    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    use VigStudio\VigSeo\Models\VigSeo;
    use VigStudio\VigSeo\Models\VigSeoTranslation;

    /**
     * @method VigSeoTranslation|null getOrPut($key, $value)
     * @method VigSeoTranslation|$this shift(int $count = 1)
     * @method VigSeoTranslation|null firstOrFail($key = null, $operator = null, $value = null)
     * @method VigSeoTranslation|$this pop(int $count = 1)
     * @method VigSeoTranslation|null pull($key, $default = null)
     * @method VigSeoTranslation|null last(callable $callback = null, $default = null)
     * @method VigSeoTranslation|$this random($number = null, bool $preserveKeys = false)
     * @method VigSeoTranslation|null sole($key = null, $operator = null, $value = null)
     * @method VigSeoTranslation|null get($key, $default = null)
     * @method VigSeoTranslation|null first(callable $callback = null, $default = null)
     * @method VigSeoTranslation|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method VigSeoTranslation|null find($key, $default = null)
     * @method VigSeoTranslation[] all()
     */
    class _IH_VigSeoTranslation_C extends _BaseCollection {
        /**
         * @param int $size
         * @return VigSeoTranslation[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method VigSeoTranslation baseSole(array|string $columns = ['*'])
     * @method VigSeoTranslation create(array $attributes = [])
     * @method VigSeoTranslation createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_VigSeoTranslation_C|VigSeoTranslation[] cursor()
     * @method VigSeoTranslation|null|_IH_VigSeoTranslation_C|VigSeoTranslation[] find($id, array|string $columns = ['*'])
     * @method _IH_VigSeoTranslation_C|VigSeoTranslation[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method VigSeoTranslation|_IH_VigSeoTranslation_C|VigSeoTranslation[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigSeoTranslation|_IH_VigSeoTranslation_C|VigSeoTranslation[] findOrFail($id, array|string $columns = ['*'])
     * @method VigSeoTranslation|_IH_VigSeoTranslation_C|VigSeoTranslation[] findOrNew($id, array|string $columns = ['*'])
     * @method VigSeoTranslation first(array|string $columns = ['*'])
     * @method VigSeoTranslation firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigSeoTranslation firstOrCreate(array $attributes = [], array $values = [])
     * @method VigSeoTranslation firstOrFail(array|string $columns = ['*'])
     * @method VigSeoTranslation firstOrNew(array $attributes = [], array $values = [])
     * @method VigSeoTranslation firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method VigSeoTranslation forceCreate(array $attributes)
     * @method VigSeoTranslation forceCreateQuietly(array $attributes = [])
     * @method _IH_VigSeoTranslation_C|VigSeoTranslation[] fromQuery(string $query, array $bindings = [])
     * @method _IH_VigSeoTranslation_C|VigSeoTranslation[] get(array|string $columns = ['*'])
     * @method VigSeoTranslation getModel()
     * @method VigSeoTranslation[] getModels(array|string $columns = ['*'])
     * @method _IH_VigSeoTranslation_C|VigSeoTranslation[] hydrate(array $items)
     * @method VigSeoTranslation make(array $attributes = [])
     * @method VigSeoTranslation newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|VigSeoTranslation[]|_IH_VigSeoTranslation_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|VigSeoTranslation[]|_IH_VigSeoTranslation_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VigSeoTranslation sole(array|string $columns = ['*'])
     * @method VigSeoTranslation updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_VigSeoTranslation_QB extends _BaseBuilder {}

    /**
     * @method VigSeo|null getOrPut($key, $value)
     * @method VigSeo|$this shift(int $count = 1)
     * @method VigSeo|null firstOrFail($key = null, $operator = null, $value = null)
     * @method VigSeo|$this pop(int $count = 1)
     * @method VigSeo|null pull($key, $default = null)
     * @method VigSeo|null last(callable $callback = null, $default = null)
     * @method VigSeo|$this random($number = null, bool $preserveKeys = false)
     * @method VigSeo|null sole($key = null, $operator = null, $value = null)
     * @method VigSeo|null get($key, $default = null)
     * @method VigSeo|null first(callable $callback = null, $default = null)
     * @method VigSeo|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method VigSeo|null find($key, $default = null)
     * @method VigSeo[] all()
     */
    class _IH_VigSeo_C extends _BaseCollection {
        /**
         * @param int $size
         * @return VigSeo[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method VigSeo baseSole(array|string $columns = ['*'])
     * @method VigSeo create(array $attributes = [])
     * @method VigSeo createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_VigSeo_C|VigSeo[] cursor()
     * @method VigSeo|null|_IH_VigSeo_C|VigSeo[] find($id, array|string $columns = ['*'])
     * @method _IH_VigSeo_C|VigSeo[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method VigSeo|_IH_VigSeo_C|VigSeo[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigSeo|_IH_VigSeo_C|VigSeo[] findOrFail($id, array|string $columns = ['*'])
     * @method VigSeo|_IH_VigSeo_C|VigSeo[] findOrNew($id, array|string $columns = ['*'])
     * @method VigSeo first(array|string $columns = ['*'])
     * @method VigSeo firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigSeo firstOrCreate(array $attributes = [], array $values = [])
     * @method VigSeo firstOrFail(array|string $columns = ['*'])
     * @method VigSeo firstOrNew(array $attributes = [], array $values = [])
     * @method VigSeo firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method VigSeo forceCreate(array $attributes)
     * @method VigSeo forceCreateQuietly(array $attributes = [])
     * @method _IH_VigSeo_C|VigSeo[] fromQuery(string $query, array $bindings = [])
     * @method _IH_VigSeo_C|VigSeo[] get(array|string $columns = ['*'])
     * @method VigSeo getModel()
     * @method VigSeo[] getModels(array|string $columns = ['*'])
     * @method _IH_VigSeo_C|VigSeo[] hydrate(array $items)
     * @method VigSeo make(array $attributes = [])
     * @method VigSeo newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|VigSeo[]|_IH_VigSeo_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|VigSeo[]|_IH_VigSeo_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VigSeo sole(array|string $columns = ['*'])
     * @method VigSeo updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_VigSeo_QB extends _BaseBuilder {}
}
