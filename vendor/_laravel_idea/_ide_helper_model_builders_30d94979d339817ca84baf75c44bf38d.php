<?php //bcefac85d468a21dc3ffe66fecb2b8b7
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\Block\Models {

    use Botble\Block\Models\Block;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method Block|null getOrPut($key, $value)
     * @method Block|$this shift(int $count = 1)
     * @method Block|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Block|$this pop(int $count = 1)
     * @method Block|null pull($key, $default = null)
     * @method Block|null last(callable $callback = null, $default = null)
     * @method Block|$this random($number = null, bool $preserveKeys = false)
     * @method Block|null sole($key = null, $operator = null, $value = null)
     * @method Block|null get($key, $default = null)
     * @method Block|null first(callable $callback = null, $default = null)
     * @method Block|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Block|null find($key, $default = null)
     * @method Block[] all()
     */
    class _IH_Block_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Block[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_Block_QB whereId($value)
     * @method _IH_Block_QB whereName($value)
     * @method _IH_Block_QB whereAlias($value)
     * @method _IH_Block_QB whereDescription($value)
     * @method _IH_Block_QB whereContent($value)
     * @method _IH_Block_QB whereStatus($value)
     * @method _IH_Block_QB whereUserId($value)
     * @method _IH_Block_QB whereCreatedAt($value)
     * @method _IH_Block_QB whereUpdatedAt($value)
     * @method Block baseSole(array|string $columns = ['*'])
     * @method Block create(array $attributes = [])
     * @method Block createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_Block_C|Block[] cursor()
     * @method Block|null|_IH_Block_C|Block[] find($id, array|string $columns = ['*'])
     * @method _IH_Block_C|Block[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Block|_IH_Block_C|Block[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Block|_IH_Block_C|Block[] findOrFail($id, array|string $columns = ['*'])
     * @method Block|_IH_Block_C|Block[] findOrNew($id, array|string $columns = ['*'])
     * @method Block first(array|string $columns = ['*'])
     * @method Block firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Block firstOrCreate(array $attributes = [], array $values = [])
     * @method Block firstOrFail(array|string $columns = ['*'])
     * @method Block firstOrNew(array $attributes = [], array $values = [])
     * @method Block firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Block forceCreate(array $attributes)
     * @method Block forceCreateQuietly(array $attributes = [])
     * @method _IH_Block_C|Block[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Block_C|Block[] get(array|string $columns = ['*'])
     * @method Block getModel()
     * @method Block[] getModels(array|string $columns = ['*'])
     * @method _IH_Block_C|Block[] hydrate(array $items)
     * @method Block make(array $attributes = [])
     * @method Block newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Block[]|_IH_Block_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|Block[]|_IH_Block_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Block sole(array|string $columns = ['*'])
     * @method Block updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Block_QB extends _BaseBuilder {}
}
