<?php //3fcce225554d249a606d6ed01f233356
/** @noinspection all */

namespace LaravelIdea\Helper\VigStudio\VigAutoTranslations\Http\Models {

    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    use VigStudio\VigAutoTranslations\Http\Models\VigTranslate;

    /**
     * @method VigTranslate|null getOrPut($key, $value)
     * @method VigTranslate|$this shift(int $count = 1)
     * @method VigTranslate|null firstOrFail($key = null, $operator = null, $value = null)
     * @method VigTranslate|$this pop(int $count = 1)
     * @method VigTranslate|null pull($key, $default = null)
     * @method VigTranslate|null last(callable $callback = null, $default = null)
     * @method VigTranslate|$this random($number = null, bool $preserveKeys = false)
     * @method VigTranslate|null sole($key = null, $operator = null, $value = null)
     * @method VigTranslate|null get($key, $default = null)
     * @method VigTranslate|null first(callable $callback = null, $default = null)
     * @method VigTranslate|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method VigTranslate|null find($key, $default = null)
     * @method VigTranslate[] all()
     */
    class _IH_VigTranslate_C extends _BaseCollection {
        /**
         * @param int $size
         * @return VigTranslate[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_VigTranslate_QB whereId($value)
     * @method _IH_VigTranslate_QB whereTextOriginal($value)
     * @method _IH_VigTranslate_QB whereTextTranslated($value)
     * @method _IH_VigTranslate_QB whereLangFrom($value)
     * @method _IH_VigTranslate_QB whereLangTo($value)
     * @method _IH_VigTranslate_QB whereCreatedAt($value)
     * @method _IH_VigTranslate_QB whereUpdatedAt($value)
     * @method VigTranslate baseSole(array|string $columns = ['*'])
     * @method VigTranslate create(array $attributes = [])
     * @method VigTranslate createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_VigTranslate_C|VigTranslate[] cursor()
     * @method VigTranslate|null|_IH_VigTranslate_C|VigTranslate[] find($id, array|string $columns = ['*'])
     * @method _IH_VigTranslate_C|VigTranslate[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method VigTranslate|_IH_VigTranslate_C|VigTranslate[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigTranslate|_IH_VigTranslate_C|VigTranslate[] findOrFail($id, array|string $columns = ['*'])
     * @method VigTranslate|_IH_VigTranslate_C|VigTranslate[] findOrNew($id, array|string $columns = ['*'])
     * @method VigTranslate first(array|string $columns = ['*'])
     * @method VigTranslate firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VigTranslate firstOrCreate(array $attributes = [], array $values = [])
     * @method VigTranslate firstOrFail(array|string $columns = ['*'])
     * @method VigTranslate firstOrNew(array $attributes = [], array $values = [])
     * @method VigTranslate firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method VigTranslate forceCreate(array $attributes)
     * @method VigTranslate forceCreateQuietly(array $attributes = [])
     * @method _IH_VigTranslate_C|VigTranslate[] fromQuery(string $query, array $bindings = [])
     * @method _IH_VigTranslate_C|VigTranslate[] get(array|string $columns = ['*'])
     * @method VigTranslate getModel()
     * @method VigTranslate[] getModels(array|string $columns = ['*'])
     * @method _IH_VigTranslate_C|VigTranslate[] hydrate(array $items)
     * @method VigTranslate make(array $attributes = [])
     * @method VigTranslate newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|VigTranslate[]|_IH_VigTranslate_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|VigTranslate[]|_IH_VigTranslate_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VigTranslate sole(array|string $columns = ['*'])
     * @method VigTranslate updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_VigTranslate_QB extends _BaseBuilder {}
}
