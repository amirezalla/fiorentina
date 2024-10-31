<?php //a23d255c6d2f75679f195395418d6809
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\CustomField\Models {

    use Botble\CustomField\Models\CustomField;
    use Botble\CustomField\Models\FieldGroup;
    use Botble\CustomField\Models\FieldItem;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method CustomField|null getOrPut($key, $value)
     * @method CustomField|$this shift(int $count = 1)
     * @method CustomField|null firstOrFail($key = null, $operator = null, $value = null)
     * @method CustomField|$this pop(int $count = 1)
     * @method CustomField|null pull($key, $default = null)
     * @method CustomField|null last(callable $callback = null, $default = null)
     * @method CustomField|$this random($number = null, bool $preserveKeys = false)
     * @method CustomField|null sole($key = null, $operator = null, $value = null)
     * @method CustomField|null get($key, $default = null)
     * @method CustomField|null first(callable $callback = null, $default = null)
     * @method CustomField|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method CustomField|null find($key, $default = null)
     * @method CustomField[] all()
     */
    class _IH_CustomField_C extends _BaseCollection {
        /**
         * @param int $size
         * @return CustomField[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_CustomField_QB whereId($value)
     * @method _IH_CustomField_QB whereUseFor($value)
     * @method _IH_CustomField_QB whereUseForId($value)
     * @method _IH_CustomField_QB whereFieldItemId($value)
     * @method _IH_CustomField_QB whereType($value)
     * @method _IH_CustomField_QB whereSlug($value)
     * @method _IH_CustomField_QB whereValue($value)
     * @method CustomField baseSole(array|string $columns = ['*'])
     * @method CustomField create(array $attributes = [])
     * @method CustomField createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_CustomField_C|CustomField[] cursor()
     * @method CustomField|null|_IH_CustomField_C|CustomField[] find($id, array|string $columns = ['*'])
     * @method _IH_CustomField_C|CustomField[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method CustomField|_IH_CustomField_C|CustomField[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method CustomField|_IH_CustomField_C|CustomField[] findOrFail($id, array|string $columns = ['*'])
     * @method CustomField|_IH_CustomField_C|CustomField[] findOrNew($id, array|string $columns = ['*'])
     * @method CustomField first(array|string $columns = ['*'])
     * @method CustomField firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method CustomField firstOrCreate(array $attributes = [], array $values = [])
     * @method CustomField firstOrFail(array|string $columns = ['*'])
     * @method CustomField firstOrNew(array $attributes = [], array $values = [])
     * @method CustomField firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method CustomField forceCreate(array $attributes)
     * @method CustomField forceCreateQuietly(array $attributes = [])
     * @method _IH_CustomField_C|CustomField[] fromQuery(string $query, array $bindings = [])
     * @method _IH_CustomField_C|CustomField[] get(array|string $columns = ['*'])
     * @method CustomField getModel()
     * @method CustomField[] getModels(array|string $columns = ['*'])
     * @method _IH_CustomField_C|CustomField[] hydrate(array $items)
     * @method CustomField make(array $attributes = [])
     * @method CustomField newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|CustomField[]|_IH_CustomField_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|CustomField[]|_IH_CustomField_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method CustomField sole(array|string $columns = ['*'])
     * @method CustomField updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_CustomField_QB extends _BaseBuilder {}

    /**
     * @method FieldGroup|null getOrPut($key, $value)
     * @method FieldGroup|$this shift(int $count = 1)
     * @method FieldGroup|null firstOrFail($key = null, $operator = null, $value = null)
     * @method FieldGroup|$this pop(int $count = 1)
     * @method FieldGroup|null pull($key, $default = null)
     * @method FieldGroup|null last(callable $callback = null, $default = null)
     * @method FieldGroup|$this random($number = null, bool $preserveKeys = false)
     * @method FieldGroup|null sole($key = null, $operator = null, $value = null)
     * @method FieldGroup|null get($key, $default = null)
     * @method FieldGroup|null first(callable $callback = null, $default = null)
     * @method FieldGroup|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method FieldGroup|null find($key, $default = null)
     * @method FieldGroup[] all()
     */
    class _IH_FieldGroup_C extends _BaseCollection {
        /**
         * @param int $size
         * @return FieldGroup[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_FieldGroup_QB whereId($value)
     * @method _IH_FieldGroup_QB whereTitle($value)
     * @method _IH_FieldGroup_QB whereRules($value)
     * @method _IH_FieldGroup_QB whereOrder($value)
     * @method _IH_FieldGroup_QB whereCreatedBy($value)
     * @method _IH_FieldGroup_QB whereUpdatedBy($value)
     * @method _IH_FieldGroup_QB whereStatus($value)
     * @method _IH_FieldGroup_QB whereCreatedAt($value)
     * @method _IH_FieldGroup_QB whereUpdatedAt($value)
     * @method FieldGroup baseSole(array|string $columns = ['*'])
     * @method FieldGroup create(array $attributes = [])
     * @method FieldGroup createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_FieldGroup_C|FieldGroup[] cursor()
     * @method FieldGroup|null|_IH_FieldGroup_C|FieldGroup[] find($id, array|string $columns = ['*'])
     * @method _IH_FieldGroup_C|FieldGroup[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method FieldGroup|_IH_FieldGroup_C|FieldGroup[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method FieldGroup|_IH_FieldGroup_C|FieldGroup[] findOrFail($id, array|string $columns = ['*'])
     * @method FieldGroup|_IH_FieldGroup_C|FieldGroup[] findOrNew($id, array|string $columns = ['*'])
     * @method FieldGroup first(array|string $columns = ['*'])
     * @method FieldGroup firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method FieldGroup firstOrCreate(array $attributes = [], array $values = [])
     * @method FieldGroup firstOrFail(array|string $columns = ['*'])
     * @method FieldGroup firstOrNew(array $attributes = [], array $values = [])
     * @method FieldGroup firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method FieldGroup forceCreate(array $attributes)
     * @method FieldGroup forceCreateQuietly(array $attributes = [])
     * @method _IH_FieldGroup_C|FieldGroup[] fromQuery(string $query, array $bindings = [])
     * @method _IH_FieldGroup_C|FieldGroup[] get(array|string $columns = ['*'])
     * @method FieldGroup getModel()
     * @method FieldGroup[] getModels(array|string $columns = ['*'])
     * @method _IH_FieldGroup_C|FieldGroup[] hydrate(array $items)
     * @method FieldGroup make(array $attributes = [])
     * @method FieldGroup newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|FieldGroup[]|_IH_FieldGroup_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|FieldGroup[]|_IH_FieldGroup_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method FieldGroup sole(array|string $columns = ['*'])
     * @method FieldGroup updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_FieldGroup_QB extends _BaseBuilder {}

    /**
     * @method FieldItem|null getOrPut($key, $value)
     * @method FieldItem|$this shift(int $count = 1)
     * @method FieldItem|null firstOrFail($key = null, $operator = null, $value = null)
     * @method FieldItem|$this pop(int $count = 1)
     * @method FieldItem|null pull($key, $default = null)
     * @method FieldItem|null last(callable $callback = null, $default = null)
     * @method FieldItem|$this random($number = null, bool $preserveKeys = false)
     * @method FieldItem|null sole($key = null, $operator = null, $value = null)
     * @method FieldItem|null get($key, $default = null)
     * @method FieldItem|null first(callable $callback = null, $default = null)
     * @method FieldItem|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method FieldItem|null find($key, $default = null)
     * @method FieldItem[] all()
     */
    class _IH_FieldItem_C extends _BaseCollection {
        /**
         * @param int $size
         * @return FieldItem[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_FieldItem_QB whereId($value)
     * @method _IH_FieldItem_QB whereFieldGroupId($value)
     * @method _IH_FieldItem_QB whereParentId($value)
     * @method _IH_FieldItem_QB whereOrder($value)
     * @method _IH_FieldItem_QB whereTitle($value)
     * @method _IH_FieldItem_QB whereSlug($value)
     * @method _IH_FieldItem_QB whereType($value)
     * @method _IH_FieldItem_QB whereInstructions($value)
     * @method _IH_FieldItem_QB whereOptions($value)
     * @method FieldItem baseSole(array|string $columns = ['*'])
     * @method FieldItem create(array $attributes = [])
     * @method FieldItem createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_FieldItem_C|FieldItem[] cursor()
     * @method FieldItem|null|_IH_FieldItem_C|FieldItem[] find($id, array|string $columns = ['*'])
     * @method _IH_FieldItem_C|FieldItem[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method FieldItem|_IH_FieldItem_C|FieldItem[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method FieldItem|_IH_FieldItem_C|FieldItem[] findOrFail($id, array|string $columns = ['*'])
     * @method FieldItem|_IH_FieldItem_C|FieldItem[] findOrNew($id, array|string $columns = ['*'])
     * @method FieldItem first(array|string $columns = ['*'])
     * @method FieldItem firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method FieldItem firstOrCreate(array $attributes = [], array $values = [])
     * @method FieldItem firstOrFail(array|string $columns = ['*'])
     * @method FieldItem firstOrNew(array $attributes = [], array $values = [])
     * @method FieldItem firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method FieldItem forceCreate(array $attributes)
     * @method FieldItem forceCreateQuietly(array $attributes = [])
     * @method _IH_FieldItem_C|FieldItem[] fromQuery(string $query, array $bindings = [])
     * @method _IH_FieldItem_C|FieldItem[] get(array|string $columns = ['*'])
     * @method FieldItem getModel()
     * @method FieldItem[] getModels(array|string $columns = ['*'])
     * @method _IH_FieldItem_C|FieldItem[] hydrate(array $items)
     * @method FieldItem make(array $attributes = [])
     * @method FieldItem newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|FieldItem[]|_IH_FieldItem_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|FieldItem[]|_IH_FieldItem_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method FieldItem sole(array|string $columns = ['*'])
     * @method FieldItem updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_FieldItem_QB extends _BaseBuilder {}
}
