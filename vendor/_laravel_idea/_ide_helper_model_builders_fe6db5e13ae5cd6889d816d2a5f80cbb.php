<?php //f88d8cd6e9b3610e0db4dcd7f49e1ad5
/** @noinspection all */

namespace LaravelIdea\Helper\Illuminate\Notifications {

    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    
    /**
     * @method DatabaseNotification baseSole(array|string $columns = ['*'])
     * @method DatabaseNotification create(array $attributes = [])
     * @method DatabaseNotification createOrFirst(array $attributes = [], array $values = [])
     * @method DatabaseNotification createOrRestore(array $attributes = [], array $values = [])
     * @method DatabaseNotificationCollection|DatabaseNotification[] cursor()
     * @method DatabaseNotification|null|DatabaseNotificationCollection|DatabaseNotification[] find($id, array|string $columns = ['*'])
     * @method DatabaseNotificationCollection|DatabaseNotification[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method DatabaseNotification|DatabaseNotificationCollection|DatabaseNotification[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method DatabaseNotification|DatabaseNotificationCollection|DatabaseNotification[] findOrFail($id, array|string $columns = ['*'])
     * @method DatabaseNotification|DatabaseNotificationCollection|DatabaseNotification[] findOrNew($id, array|string $columns = ['*'])
     * @method DatabaseNotification first(array|string $columns = ['*'])
     * @method DatabaseNotification firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method DatabaseNotification firstOrCreate(array $attributes = [], array $values = [])
     * @method DatabaseNotification firstOrFail(array|string $columns = ['*'])
     * @method DatabaseNotification firstOrNew(array $attributes = [], array $values = [])
     * @method DatabaseNotification firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method DatabaseNotification forceCreate(array $attributes)
     * @method DatabaseNotification forceCreateQuietly(array $attributes = [])
     * @method DatabaseNotificationCollection|DatabaseNotification[] fromQuery(string $query, array $bindings = [])
     * @method DatabaseNotificationCollection|DatabaseNotification[] get(array|string $columns = ['*'])
     * @method DatabaseNotification getModel()
     * @method DatabaseNotification[] getModels(array|string $columns = ['*'])
     * @method DatabaseNotificationCollection|DatabaseNotification[] hydrate(array $items)
     * @method DatabaseNotification make(array $attributes = [])
     * @method DatabaseNotification newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|DatabaseNotification[]|DatabaseNotificationCollection paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method DatabaseNotification restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|DatabaseNotification[]|DatabaseNotificationCollection simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method DatabaseNotification sole(array|string $columns = ['*'])
     * @method DatabaseNotification updateOrCreate(array $attributes, array $values = [])
     * @method _IH_DatabaseNotification_QB read()
     * @method _IH_DatabaseNotification_QB unread()
     */
    class _IH_DatabaseNotification_QB extends _BaseBuilder {}
}