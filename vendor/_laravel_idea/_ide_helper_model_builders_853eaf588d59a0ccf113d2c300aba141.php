<?php //3d8ab7362bd133b0784f76320e43ef44
/** @noinspection all */

namespace Botble\Base\Models {

    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_AdminNotification_C;
    use LaravelIdea\Helper\_BaseBuilder;

    /**
     * @method AdminNotificationQueryBuilder whereId($value)
     * @method AdminNotificationQueryBuilder whereTitle($value)
     * @method AdminNotificationQueryBuilder whereActionLabel($value)
     * @method AdminNotificationQueryBuilder whereActionUrl($value)
     * @method AdminNotificationQueryBuilder whereDescription($value)
     * @method AdminNotificationQueryBuilder whereReadAt($value)
     * @method AdminNotificationQueryBuilder whereCreatedAt($value)
     * @method AdminNotificationQueryBuilder whereUpdatedAt($value)
     * @method AdminNotificationQueryBuilder wherePermission($value)
     * @method AdminNotification baseSole(array|string $columns = ['*'])
     * @method AdminNotification create(array $attributes = [])
     * @method AdminNotification createOrFirst(array $attributes = [], array $values = [])
     * @method _IH_AdminNotification_C|AdminNotification[] cursor()
     * @method AdminNotification|null|_IH_AdminNotification_C|AdminNotification[] find($id, array|string $columns = ['*'])
     * @method _IH_AdminNotification_C|AdminNotification[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AdminNotification|_IH_AdminNotification_C|AdminNotification[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdminNotification|_IH_AdminNotification_C|AdminNotification[] findOrFail($id, array|string $columns = ['*'])
     * @method AdminNotification|_IH_AdminNotification_C|AdminNotification[] findOrNew($id, array|string $columns = ['*'])
     * @method AdminNotification first(array|string $columns = ['*'])
     * @method AdminNotification firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdminNotification firstOrCreate(array $attributes = [], array $values = [])
     * @method AdminNotification firstOrFail(array|string $columns = ['*'])
     * @method AdminNotification firstOrNew(array $attributes = [], array $values = [])
     * @method AdminNotification firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AdminNotification forceCreate(array $attributes)
     * @method AdminNotification forceCreateQuietly(array $attributes = [])
     * @method _IH_AdminNotification_C|AdminNotification[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AdminNotification_C|AdminNotification[] get(array|string $columns = ['*'])
     * @method AdminNotification getModel()
     * @method AdminNotification[] getModels(array|string $columns = ['*'])
     * @method _IH_AdminNotification_C|AdminNotification[] hydrate(array $items)
     * @method AdminNotification make(array $attributes = [])
     * @method AdminNotification newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AdminNotification[]|_IH_AdminNotification_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AdminNotification[]|_IH_AdminNotification_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AdminNotification sole(array|string $columns = ['*'])
     * @method AdminNotification updateOrCreate(array $attributes, array $values = [])
     */
    class AdminNotificationQueryBuilder extends _BaseBuilder {}
}
