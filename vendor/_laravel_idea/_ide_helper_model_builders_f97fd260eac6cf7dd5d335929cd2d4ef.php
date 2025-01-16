<?php //c4db3152b2e49bca5d37020cf87d39cf
/** @noinspection all */

namespace LaravelIdea\Helper\Botble\Member\Models {

    use Botble\Member\Models\Member;
    use Botble\Member\Models\MemberActivityLog;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method MemberActivityLog|null getOrPut($key, $value)
     * @method MemberActivityLog|$this shift(int $count = 1)
     * @method MemberActivityLog|null firstOrFail($key = null, $operator = null, $value = null)
     * @method MemberActivityLog|$this pop(int $count = 1)
     * @method MemberActivityLog|null pull($key, $default = null)
     * @method MemberActivityLog|null last(callable $callback = null, $default = null)
     * @method MemberActivityLog|$this random($number = null, bool $preserveKeys = false)
     * @method MemberActivityLog|null sole($key = null, $operator = null, $value = null)
     * @method MemberActivityLog|null get($key, $default = null)
     * @method MemberActivityLog|null first(callable $callback = null, $default = null)
     * @method MemberActivityLog|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method MemberActivityLog|null find($key, $default = null)
     * @method MemberActivityLog[] all()
     */
    class _IH_MemberActivityLog_C extends _BaseCollection {
        /**
         * @param int $size
         * @return MemberActivityLog[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_MemberActivityLog_QB whereId($value)
     * @method _IH_MemberActivityLog_QB whereAction($value)
     * @method _IH_MemberActivityLog_QB whereUserAgent($value)
     * @method _IH_MemberActivityLog_QB whereReferenceUrl($value)
     * @method _IH_MemberActivityLog_QB whereReferenceName($value)
     * @method _IH_MemberActivityLog_QB whereIpAddress($value)
     * @method _IH_MemberActivityLog_QB whereMemberId($value)
     * @method _IH_MemberActivityLog_QB whereCreatedAt($value)
     * @method _IH_MemberActivityLog_QB whereUpdatedAt($value)
     * @method MemberActivityLog baseSole(array|string $columns = ['*'])
     * @method MemberActivityLog create(array $attributes = [])
     * @method MemberActivityLog createOrFirst(array $attributes = [], array $values = [])
     * @method MemberActivityLog createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_MemberActivityLog_C|MemberActivityLog[] cursor()
     * @method MemberActivityLog|null|_IH_MemberActivityLog_C|MemberActivityLog[] find($id, array|string $columns = ['*'])
     * @method _IH_MemberActivityLog_C|MemberActivityLog[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method MemberActivityLog|_IH_MemberActivityLog_C|MemberActivityLog[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MemberActivityLog|_IH_MemberActivityLog_C|MemberActivityLog[] findOrFail($id, array|string $columns = ['*'])
     * @method MemberActivityLog|_IH_MemberActivityLog_C|MemberActivityLog[] findOrNew($id, array|string $columns = ['*'])
     * @method MemberActivityLog first(array|string $columns = ['*'])
     * @method MemberActivityLog firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MemberActivityLog firstOrCreate(array $attributes = [], array $values = [])
     * @method MemberActivityLog firstOrFail(array|string $columns = ['*'])
     * @method MemberActivityLog firstOrNew(array $attributes = [], array $values = [])
     * @method MemberActivityLog firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method MemberActivityLog forceCreate(array $attributes)
     * @method MemberActivityLog forceCreateQuietly(array $attributes = [])
     * @method _IH_MemberActivityLog_C|MemberActivityLog[] fromQuery(string $query, array $bindings = [])
     * @method _IH_MemberActivityLog_C|MemberActivityLog[] get(array|string $columns = ['*'])
     * @method MemberActivityLog getModel()
     * @method MemberActivityLog[] getModels(array|string $columns = ['*'])
     * @method _IH_MemberActivityLog_C|MemberActivityLog[] hydrate(array $items)
     * @method MemberActivityLog make(array $attributes = [])
     * @method MemberActivityLog newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|MemberActivityLog[]|_IH_MemberActivityLog_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MemberActivityLog restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|MemberActivityLog[]|_IH_MemberActivityLog_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MemberActivityLog sole(array|string $columns = ['*'])
     * @method MemberActivityLog updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_MemberActivityLog_QB extends _BaseBuilder {}
    
    /**
     * @method Member|null getOrPut($key, $value)
     * @method Member|$this shift(int $count = 1)
     * @method Member|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Member|$this pop(int $count = 1)
     * @method Member|null pull($key, $default = null)
     * @method Member|null last(callable $callback = null, $default = null)
     * @method Member|$this random($number = null, bool $preserveKeys = false)
     * @method Member|null sole($key = null, $operator = null, $value = null)
     * @method Member|null get($key, $default = null)
     * @method Member|null first(callable $callback = null, $default = null)
     * @method Member|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Member|null find($key, $default = null)
     * @method Member[] all()
     */
    class _IH_Member_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Member[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Member_QB whereId($value)
     * @method _IH_Member_QB whereFirstName($value)
     * @method _IH_Member_QB whereLastName($value)
     * @method _IH_Member_QB whereDescription($value)
     * @method _IH_Member_QB whereGender($value)
     * @method _IH_Member_QB whereEmail($value)
     * @method _IH_Member_QB wherePassword($value)
     * @method _IH_Member_QB whereAvatarId($value)
     * @method _IH_Member_QB whereDob($value)
     * @method _IH_Member_QB wherePhone($value)
     * @method _IH_Member_QB whereConfirmedAt($value)
     * @method _IH_Member_QB whereEmailVerifyToken($value)
     * @method _IH_Member_QB whereRememberToken($value)
     * @method _IH_Member_QB whereCreatedAt($value)
     * @method _IH_Member_QB whereUpdatedAt($value)
     * @method _IH_Member_QB whereStatus($value)
     * @method Member baseSole(array|string $columns = ['*'])
     * @method Member create(array $attributes = [])
     * @method Member createOrFirst(array $attributes = [], array $values = [])
     * @method Member createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Member_C|Member[] cursor()
     * @method Member|null|_IH_Member_C|Member[] find($id, array|string $columns = ['*'])
     * @method _IH_Member_C|Member[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Member|_IH_Member_C|Member[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Member|_IH_Member_C|Member[] findOrFail($id, array|string $columns = ['*'])
     * @method Member|_IH_Member_C|Member[] findOrNew($id, array|string $columns = ['*'])
     * @method Member first(array|string $columns = ['*'])
     * @method Member firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Member firstOrCreate(array $attributes = [], array $values = [])
     * @method Member firstOrFail(array|string $columns = ['*'])
     * @method Member firstOrNew(array $attributes = [], array $values = [])
     * @method Member firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Member forceCreate(array $attributes)
     * @method Member forceCreateQuietly(array $attributes = [])
     * @method _IH_Member_C|Member[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Member_C|Member[] get(array|string $columns = ['*'])
     * @method Member getModel()
     * @method Member[] getModels(array|string $columns = ['*'])
     * @method _IH_Member_C|Member[] hydrate(array $items)
     * @method Member make(array $attributes = [])
     * @method Member newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Member[]|_IH_Member_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Member restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Member[]|_IH_Member_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Member sole(array|string $columns = ['*'])
     * @method Member updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Member_QB extends _BaseBuilder {}
}