<?php //b6ebe6d821ee4be6f936ae18482f1d70
/** @noinspection all */

namespace LaravelIdea\Helper\ArchiElite\IpBlocker\Models {

    use ArchiElite\IpBlocker\Models\History;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method History|null getOrPut($key, $value)
     * @method History|$this shift(int $count = 1)
     * @method History|null firstOrFail($key = null, $operator = null, $value = null)
     * @method History|$this pop(int $count = 1)
     * @method History|null pull($key, $default = null)
     * @method History|null last(callable $callback = null, $default = null)
     * @method History|$this random($number = null, bool $preserveKeys = false)
     * @method History|null sole($key = null, $operator = null, $value = null)
     * @method History|null get($key, $default = null)
     * @method History|null first(callable $callback = null, $default = null)
     * @method History|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method History|null find($key, $default = null)
     * @method History[] all()
     */
    class _IH_History_C extends _BaseCollection {
        /**
         * @param int $size
         * @return History[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_History_QB whereId($value)
     * @method _IH_History_QB whereIpAddress($value)
     * @method _IH_History_QB whereCountRequests($value)
     * @method _IH_History_QB whereCreatedAt($value)
     * @method _IH_History_QB whereUpdatedAt($value)
     * @method History baseSole(array|string $columns = ['*'])
     * @method History create(array $attributes = [])
     * @method History createOrFirst(array $attributes = [], array $values = [])
     * @method History createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_History_C|History[] cursor()
     * @method History|null|_IH_History_C|History[] find($id, array|string $columns = ['*'])
     * @method _IH_History_C|History[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method History|_IH_History_C|History[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method History|_IH_History_C|History[] findOrFail($id, array|string $columns = ['*'])
     * @method History|_IH_History_C|History[] findOrNew($id, array|string $columns = ['*'])
     * @method History first(array|string $columns = ['*'])
     * @method History firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method History firstOrCreate(array $attributes = [], array $values = [])
     * @method History firstOrFail(array|string $columns = ['*'])
     * @method History firstOrNew(array $attributes = [], array $values = [])
     * @method History firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method History forceCreate(array $attributes)
     * @method History forceCreateQuietly(array $attributes = [])
     * @method _IH_History_C|History[] fromQuery(string $query, array $bindings = [])
     * @method _IH_History_C|History[] get(array|string $columns = ['*'])
     * @method History getModel()
     * @method History[] getModels(array|string $columns = ['*'])
     * @method _IH_History_C|History[] hydrate(array $items)
     * @method History make(array $attributes = [])
     * @method History newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|History[]|_IH_History_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method History restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|History[]|_IH_History_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method History sole(array|string $columns = ['*'])
     * @method History updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_History_QB extends _BaseBuilder {}
}