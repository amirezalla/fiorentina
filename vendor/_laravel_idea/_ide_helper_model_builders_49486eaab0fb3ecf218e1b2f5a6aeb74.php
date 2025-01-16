<?php //042339daf2640f596f410e5d8aa52900
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\RequestLog\Models {

    use Botble\RequestLog\Models\RequestLog;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method RequestLog|null getOrPut($key, $value)
     * @method RequestLog|$this shift(int $count = 1)
     * @method RequestLog|null firstOrFail($key = null, $operator = null, $value = null)
     * @method RequestLog|$this pop(int $count = 1)
     * @method RequestLog|null pull($key, $default = null)
     * @method RequestLog|null last(callable $callback = null, $default = null)
     * @method RequestLog|$this random($number = null, bool $preserveKeys = false)
     * @method RequestLog|null sole($key = null, $operator = null, $value = null)
     * @method RequestLog|null get($key, $default = null)
     * @method RequestLog|null first(callable $callback = null, $default = null)
     * @method RequestLog|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method RequestLog|null find($key, $default = null)
     * @method RequestLog[] all()
     */
    class _IH_RequestLog_C extends _BaseCollection {
        /**
         * @param int $size
         * @return RequestLog[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_RequestLog_QB whereId($value)
     * @method _IH_RequestLog_QB whereStatusCode($value)
     * @method _IH_RequestLog_QB whereUrl($value)
     * @method _IH_RequestLog_QB whereCount($value)
     * @method _IH_RequestLog_QB whereUserId($value)
     * @method _IH_RequestLog_QB whereReferrer($value)
     * @method _IH_RequestLog_QB whereCreatedAt($value)
     * @method _IH_RequestLog_QB whereUpdatedAt($value)
     * @method RequestLog baseSole(array|string $columns = ['*'])
     * @method RequestLog create(array $attributes = [])
     * @method RequestLog createOrFirst(array $attributes = [], array $values = [])
     * @method RequestLog createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_RequestLog_C|RequestLog[] cursor()
     * @method RequestLog|null|_IH_RequestLog_C|RequestLog[] find($id, array|string $columns = ['*'])
     * @method _IH_RequestLog_C|RequestLog[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method RequestLog|_IH_RequestLog_C|RequestLog[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method RequestLog|_IH_RequestLog_C|RequestLog[] findOrFail($id, array|string $columns = ['*'])
     * @method RequestLog|_IH_RequestLog_C|RequestLog[] findOrNew($id, array|string $columns = ['*'])
     * @method RequestLog first(array|string $columns = ['*'])
     * @method RequestLog firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method RequestLog firstOrCreate(array $attributes = [], array $values = [])
     * @method RequestLog firstOrFail(array|string $columns = ['*'])
     * @method RequestLog firstOrNew(array $attributes = [], array $values = [])
     * @method RequestLog firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method RequestLog forceCreate(array $attributes)
     * @method RequestLog forceCreateQuietly(array $attributes = [])
     * @method _IH_RequestLog_C|RequestLog[] fromQuery(string $query, array $bindings = [])
     * @method _IH_RequestLog_C|RequestLog[] get(array|string $columns = ['*'])
     * @method RequestLog getModel()
     * @method RequestLog[] getModels(array|string $columns = ['*'])
     * @method _IH_RequestLog_C|RequestLog[] hydrate(array $items)
     * @method RequestLog make(array $attributes = [])
     * @method RequestLog newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|RequestLog[]|_IH_RequestLog_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method RequestLog restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|RequestLog[]|_IH_RequestLog_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method RequestLog sole(array|string $columns = ['*'])
     * @method RequestLog updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_RequestLog_QB extends _BaseBuilder {}
}