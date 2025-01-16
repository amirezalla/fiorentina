<?php //b54b8b3d23384321809e180230d59f7c
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\Setting\Models {

    use Botble\Setting\Models\Setting;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method Setting|null getOrPut($key, $value)
     * @method Setting|$this shift(int $count = 1)
     * @method Setting|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Setting|$this pop(int $count = 1)
     * @method Setting|null pull($key, $default = null)
     * @method Setting|null last(callable $callback = null, $default = null)
     * @method Setting|$this random($number = null, bool $preserveKeys = false)
     * @method Setting|null sole($key = null, $operator = null, $value = null)
     * @method Setting|null get($key, $default = null)
     * @method Setting|null first(callable $callback = null, $default = null)
     * @method Setting|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Setting|null find($key, $default = null)
     * @method Setting[] all()
     */
    class _IH_Setting_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Setting[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Setting_QB whereId($value)
     * @method _IH_Setting_QB whereValue($value)
     * @method _IH_Setting_QB whereCreatedAt($value)
     * @method _IH_Setting_QB whereUpdatedAt($value)
     * @method Setting baseSole(array|string $columns = ['*'])
     * @method Setting create(array $attributes = [])
     * @method Setting createOrFirst(array $attributes = [], array $values = [])
     * @method Setting createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Setting_C|Setting[] cursor()
     * @method Setting|null|_IH_Setting_C|Setting[] find($id, array|string $columns = ['*'])
     * @method _IH_Setting_C|Setting[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Setting|_IH_Setting_C|Setting[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Setting|_IH_Setting_C|Setting[] findOrFail($id, array|string $columns = ['*'])
     * @method Setting|_IH_Setting_C|Setting[] findOrNew($id, array|string $columns = ['*'])
     * @method Setting first(array|string $columns = ['*'])
     * @method Setting firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Setting firstOrCreate(array $attributes = [], array $values = [])
     * @method Setting firstOrFail(array|string $columns = ['*'])
     * @method Setting firstOrNew(array $attributes = [], array $values = [])
     * @method Setting firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Setting forceCreate(array $attributes)
     * @method Setting forceCreateQuietly(array $attributes = [])
     * @method _IH_Setting_C|Setting[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Setting_C|Setting[] get(array|string $columns = ['*'])
     * @method Setting getModel()
     * @method Setting[] getModels(array|string $columns = ['*'])
     * @method _IH_Setting_C|Setting[] hydrate(array $items)
     * @method Setting make(array $attributes = [])
     * @method Setting newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Setting[]|_IH_Setting_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Setting restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Setting[]|_IH_Setting_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Setting sole(array|string $columns = ['*'])
     * @method Setting updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Setting_QB extends _BaseBuilder {}
}