<?php //9e16cbf07848d2fdddf1e9f1abeef195
/** @noinspection all */

namespace LaravelIdea\Helper\FriendsOfBotble\Comment\Models {

    use FriendsOfBotble\Comment\Models\Comment;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method Comment|null getOrPut($key, $value)
     * @method Comment|$this shift(int $count = 1)
     * @method Comment|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Comment|$this pop(int $count = 1)
     * @method Comment|null pull($key, $default = null)
     * @method Comment|null last(callable $callback = null, $default = null)
     * @method Comment|$this random($number = null, bool $preserveKeys = false)
     * @method Comment|null sole($key = null, $operator = null, $value = null)
     * @method Comment|null get($key, $default = null)
     * @method Comment|null first(callable $callback = null, $default = null)
     * @method Comment|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Comment|null find($key, $default = null)
     * @method Comment[] all()
     */
    class _IH_Comment_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Comment[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }

    /**
     * @method _IH_Comment_QB whereId($value)
     * @method _IH_Comment_QB whereReplyTo($value)
     * @method _IH_Comment_QB whereAuthorId($value)
     * @method _IH_Comment_QB whereAuthorType($value)
     * @method _IH_Comment_QB whereReferenceId($value)
     * @method _IH_Comment_QB whereReferenceType($value)
     * @method _IH_Comment_QB whereReferenceUrl($value)
     * @method _IH_Comment_QB whereName($value)
     * @method _IH_Comment_QB whereEmail($value)
     * @method _IH_Comment_QB whereWebsite($value)
     * @method _IH_Comment_QB whereContent($value)
     * @method _IH_Comment_QB whereStatus($value)
     * @method _IH_Comment_QB whereIpAddress($value)
     * @method _IH_Comment_QB whereUserAgent($value)
     * @method _IH_Comment_QB whereCreatedAt($value)
     * @method _IH_Comment_QB whereUpdatedAt($value)
     * @method Comment baseSole(array|string $columns = ['*'])
     * @method Comment create(array $attributes = [])
     * @method Comment createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_Comment_C|Comment[] cursor()
     * @method Comment|null|_IH_Comment_C|Comment[] find($id, array|string $columns = ['*'])
     * @method _IH_Comment_C|Comment[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Comment|_IH_Comment_C|Comment[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Comment|_IH_Comment_C|Comment[] findOrFail($id, array|string $columns = ['*'])
     * @method Comment|_IH_Comment_C|Comment[] findOrNew($id, array|string $columns = ['*'])
     * @method Comment first(array|string $columns = ['*'])
     * @method Comment firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Comment firstOrCreate(array $attributes = [], array $values = [])
     * @method Comment firstOrFail(array|string $columns = ['*'])
     * @method Comment firstOrNew(array $attributes = [], array $values = [])
     * @method Comment firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Comment forceCreate(array $attributes)
     * @method Comment forceCreateQuietly(array $attributes = [])
     * @method _IH_Comment_C|Comment[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Comment_C|Comment[] get(array|string $columns = ['*'])
     * @method Comment getModel()
     * @method Comment[] getModels(array|string $columns = ['*'])
     * @method _IH_Comment_C|Comment[] hydrate(array $items)
     * @method Comment make(array $attributes = [])
     * @method Comment newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Comment[]|_IH_Comment_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|Comment[]|_IH_Comment_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Comment sole(array|string $columns = ['*'])
     * @method Comment updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Comment_QB extends _BaseBuilder {}
}
