<?php //c1a72721fd6cd83e78f5f4db63902c11
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\LanguageAdvanced\Models {

    use Botble\LanguageAdvanced\Models\TranslationResolver;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method TranslationResolver|null getOrPut($key, $value)
     * @method TranslationResolver|$this shift(int $count = 1)
     * @method TranslationResolver|null firstOrFail($key = null, $operator = null, $value = null)
     * @method TranslationResolver|$this pop(int $count = 1)
     * @method TranslationResolver|null pull($key, $default = null)
     * @method TranslationResolver|null last(callable $callback = null, $default = null)
     * @method TranslationResolver|$this random($number = null, bool $preserveKeys = false)
     * @method TranslationResolver|null sole($key = null, $operator = null, $value = null)
     * @method TranslationResolver|null get($key, $default = null)
     * @method TranslationResolver|null first(callable $callback = null, $default = null)
     * @method TranslationResolver|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method TranslationResolver|null find($key, $default = null)
     * @method TranslationResolver[] all()
     */
    class _IH_TranslationResolver_C extends _BaseCollection {
        /**
         * @param int $size
         * @return TranslationResolver[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method TranslationResolver baseSole(array|string $columns = ['*'])
     * @method TranslationResolver create(array $attributes = [])
     * @method TranslationResolver createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_TranslationResolver_C|TranslationResolver[] cursor()
     * @method TranslationResolver|null|_IH_TranslationResolver_C|TranslationResolver[] find($id, array|string $columns = ['*'])
     * @method _IH_TranslationResolver_C|TranslationResolver[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method TranslationResolver|_IH_TranslationResolver_C|TranslationResolver[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method TranslationResolver|_IH_TranslationResolver_C|TranslationResolver[] findOrFail($id, array|string $columns = ['*'])
     * @method TranslationResolver|_IH_TranslationResolver_C|TranslationResolver[] findOrNew($id, array|string $columns = ['*'])
     * @method TranslationResolver first(array|string $columns = ['*'])
     * @method TranslationResolver firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method TranslationResolver firstOrCreate(array $attributes = [], array $values = [])
     * @method TranslationResolver firstOrFail(array|string $columns = ['*'])
     * @method TranslationResolver firstOrNew(array $attributes = [], array $values = [])
     * @method TranslationResolver firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method TranslationResolver forceCreate(array $attributes)
     * @method TranslationResolver forceCreateQuietly(array $attributes = [])
     * @method _IH_TranslationResolver_C|TranslationResolver[] fromQuery(string $query, array $bindings = [])
     * @method _IH_TranslationResolver_C|TranslationResolver[] get(array|string $columns = ['*'])
     * @method TranslationResolver getModel()
     * @method TranslationResolver[] getModels(array|string $columns = ['*'])
     * @method _IH_TranslationResolver_C|TranslationResolver[] hydrate(array $items)
     * @method TranslationResolver make(array $attributes = [])
     * @method TranslationResolver newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|TranslationResolver[]|_IH_TranslationResolver_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|TranslationResolver[]|_IH_TranslationResolver_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method TranslationResolver sole(array|string $columns = ['*'])
     * @method TranslationResolver updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_TranslationResolver_QB extends _BaseBuilder {}
}
