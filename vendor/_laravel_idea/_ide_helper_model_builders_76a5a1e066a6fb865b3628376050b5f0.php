<?php //e1ed93fc75b30ac421b58644fb2cfdb0
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models {

    use App\Models\Ad;
    use App\Models\AdPosition;
    use App\Models\AdType;
    use App\Models\Calendario;
    use App\Models\LiveChat;
    use App\Models\MatchCommentary;
    use App\Models\Matches;
    use App\Models\MatchLineups;
    use App\Models\MatchStatics;
    use App\Models\MatchSummary;
    use App\Models\Message;
    use App\Models\Notifica;
    use App\Models\Player;
    use App\Models\PlayerStats;
    use App\Models\PlayerVotes;
    use App\Models\Poll;
    use App\Models\PollOption;
    use App\Models\Standing;
    use App\Models\User;
    use App\Models\Video;
    use App\Models\VideoAd;
    use App\Models\VideoSpec;
    use App\Models\Vote;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method AdPosition|null getOrPut($key, $value)
     * @method AdPosition|$this shift(int $count = 1)
     * @method AdPosition|null firstOrFail($key = null, $operator = null, $value = null)
     * @method AdPosition|$this pop(int $count = 1)
     * @method AdPosition|null pull($key, $default = null)
     * @method AdPosition|null last(callable $callback = null, $default = null)
     * @method AdPosition|$this random($number = null, bool $preserveKeys = false)
     * @method AdPosition|null sole($key = null, $operator = null, $value = null)
     * @method AdPosition|null get($key, $default = null)
     * @method AdPosition|null first(callable $callback = null, $default = null)
     * @method AdPosition|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AdPosition|null find($key, $default = null)
     * @method AdPosition[] all()
     */
    class _IH_AdPosition_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AdPosition[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method AdPosition baseSole(array|string $columns = ['*'])
     * @method AdPosition create(array $attributes = [])
     * @method AdPosition createOrFirst(array $attributes = [], array $values = [])
     * @method AdPosition createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_AdPosition_C|AdPosition[] cursor()
     * @method AdPosition|null|_IH_AdPosition_C|AdPosition[] find($id, array|string $columns = ['*'])
     * @method _IH_AdPosition_C|AdPosition[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AdPosition|_IH_AdPosition_C|AdPosition[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdPosition|_IH_AdPosition_C|AdPosition[] findOrFail($id, array|string $columns = ['*'])
     * @method AdPosition|_IH_AdPosition_C|AdPosition[] findOrNew($id, array|string $columns = ['*'])
     * @method AdPosition first(array|string $columns = ['*'])
     * @method AdPosition firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdPosition firstOrCreate(array $attributes = [], array $values = [])
     * @method AdPosition firstOrFail(array|string $columns = ['*'])
     * @method AdPosition firstOrNew(array $attributes = [], array $values = [])
     * @method AdPosition firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AdPosition forceCreate(array $attributes)
     * @method AdPosition forceCreateQuietly(array $attributes = [])
     * @method _IH_AdPosition_C|AdPosition[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AdPosition_C|AdPosition[] get(array|string $columns = ['*'])
     * @method AdPosition getModel()
     * @method AdPosition[] getModels(array|string $columns = ['*'])
     * @method _IH_AdPosition_C|AdPosition[] hydrate(array $items)
     * @method AdPosition make(array $attributes = [])
     * @method AdPosition newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AdPosition[]|_IH_AdPosition_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AdPosition restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|AdPosition[]|_IH_AdPosition_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AdPosition sole(array|string $columns = ['*'])
     * @method AdPosition updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AdPosition_QB extends _BaseBuilder {}
    
    /**
     * @method AdType|null getOrPut($key, $value)
     * @method AdType|$this shift(int $count = 1)
     * @method AdType|null firstOrFail($key = null, $operator = null, $value = null)
     * @method AdType|$this pop(int $count = 1)
     * @method AdType|null pull($key, $default = null)
     * @method AdType|null last(callable $callback = null, $default = null)
     * @method AdType|$this random($number = null, bool $preserveKeys = false)
     * @method AdType|null sole($key = null, $operator = null, $value = null)
     * @method AdType|null get($key, $default = null)
     * @method AdType|null first(callable $callback = null, $default = null)
     * @method AdType|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AdType|null find($key, $default = null)
     * @method AdType[] all()
     */
    class _IH_AdType_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AdType[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method AdType baseSole(array|string $columns = ['*'])
     * @method AdType create(array $attributes = [])
     * @method AdType createOrFirst(array $attributes = [], array $values = [])
     * @method AdType createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_AdType_C|AdType[] cursor()
     * @method AdType|null|_IH_AdType_C|AdType[] find($id, array|string $columns = ['*'])
     * @method _IH_AdType_C|AdType[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AdType|_IH_AdType_C|AdType[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdType|_IH_AdType_C|AdType[] findOrFail($id, array|string $columns = ['*'])
     * @method AdType|_IH_AdType_C|AdType[] findOrNew($id, array|string $columns = ['*'])
     * @method AdType first(array|string $columns = ['*'])
     * @method AdType firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AdType firstOrCreate(array $attributes = [], array $values = [])
     * @method AdType firstOrFail(array|string $columns = ['*'])
     * @method AdType firstOrNew(array $attributes = [], array $values = [])
     * @method AdType firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AdType forceCreate(array $attributes)
     * @method AdType forceCreateQuietly(array $attributes = [])
     * @method _IH_AdType_C|AdType[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AdType_C|AdType[] get(array|string $columns = ['*'])
     * @method AdType getModel()
     * @method AdType[] getModels(array|string $columns = ['*'])
     * @method _IH_AdType_C|AdType[] hydrate(array $items)
     * @method AdType make(array $attributes = [])
     * @method AdType newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AdType[]|_IH_AdType_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AdType restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|AdType[]|_IH_AdType_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AdType sole(array|string $columns = ['*'])
     * @method AdType updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AdType_QB extends _BaseBuilder {}
    
    /**
     * @method Ad|null getOrPut($key, $value)
     * @method Ad|$this shift(int $count = 1)
     * @method Ad|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Ad|$this pop(int $count = 1)
     * @method Ad|null pull($key, $default = null)
     * @method Ad|null last(callable $callback = null, $default = null)
     * @method Ad|$this random($number = null, bool $preserveKeys = false)
     * @method Ad|null sole($key = null, $operator = null, $value = null)
     * @method Ad|null get($key, $default = null)
     * @method Ad|null first(callable $callback = null, $default = null)
     * @method Ad|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Ad|null find($key, $default = null)
     * @method Ad[] all()
     */
    class _IH_Ad_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Ad[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Ad baseSole(array|string $columns = ['*'])
     * @method Ad create(array $attributes = [])
     * @method Ad createOrFirst(array $attributes = [], array $values = [])
     * @method Ad createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Ad_C|Ad[] cursor()
     * @method Ad|null|_IH_Ad_C|Ad[] find($id, array|string $columns = ['*'])
     * @method _IH_Ad_C|Ad[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Ad|_IH_Ad_C|Ad[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Ad|_IH_Ad_C|Ad[] findOrFail($id, array|string $columns = ['*'])
     * @method Ad|_IH_Ad_C|Ad[] findOrNew($id, array|string $columns = ['*'])
     * @method Ad first(array|string $columns = ['*'])
     * @method Ad firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Ad firstOrCreate(array $attributes = [], array $values = [])
     * @method Ad firstOrFail(array|string $columns = ['*'])
     * @method Ad firstOrNew(array $attributes = [], array $values = [])
     * @method Ad firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Ad forceCreate(array $attributes)
     * @method Ad forceCreateQuietly(array $attributes = [])
     * @method _IH_Ad_C|Ad[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Ad_C|Ad[] get(array|string $columns = ['*'])
     * @method Ad getModel()
     * @method Ad[] getModels(array|string $columns = ['*'])
     * @method _IH_Ad_C|Ad[] hydrate(array $items)
     * @method Ad make(array $attributes = [])
     * @method Ad newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Ad[]|_IH_Ad_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Ad restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Ad[]|_IH_Ad_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Ad sole(array|string $columns = ['*'])
     * @method Ad updateOrCreate(array $attributes, array $values = [])
     * @method _IH_Ad_QB inRandomOrderByWeight()
     * @method _IH_Ad_QB typeAnnuncioImmagine()
     * @method _IH_Ad_QB typeGoogleAds()
     */
    class _IH_Ad_QB extends _BaseBuilder {}
    
    /**
     * @method Calendario|null getOrPut($key, $value)
     * @method Calendario|$this shift(int $count = 1)
     * @method Calendario|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Calendario|$this pop(int $count = 1)
     * @method Calendario|null pull($key, $default = null)
     * @method Calendario|null last(callable $callback = null, $default = null)
     * @method Calendario|$this random($number = null, bool $preserveKeys = false)
     * @method Calendario|null sole($key = null, $operator = null, $value = null)
     * @method Calendario|null get($key, $default = null)
     * @method Calendario|null first(callable $callback = null, $default = null)
     * @method Calendario|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Calendario|null find($key, $default = null)
     * @method Calendario[] all()
     */
    class _IH_Calendario_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Calendario[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Calendario baseSole(array|string $columns = ['*'])
     * @method Calendario create(array $attributes = [])
     * @method Calendario createOrFirst(array $attributes = [], array $values = [])
     * @method Calendario createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Calendario_C|Calendario[] cursor()
     * @method Calendario|null|_IH_Calendario_C|Calendario[] find($id, array|string $columns = ['*'])
     * @method _IH_Calendario_C|Calendario[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Calendario|_IH_Calendario_C|Calendario[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Calendario|_IH_Calendario_C|Calendario[] findOrFail($id, array|string $columns = ['*'])
     * @method Calendario|_IH_Calendario_C|Calendario[] findOrNew($id, array|string $columns = ['*'])
     * @method Calendario first(array|string $columns = ['*'])
     * @method Calendario firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Calendario firstOrCreate(array $attributes = [], array $values = [])
     * @method Calendario firstOrFail(array|string $columns = ['*'])
     * @method Calendario firstOrNew(array $attributes = [], array $values = [])
     * @method Calendario firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Calendario forceCreate(array $attributes)
     * @method Calendario forceCreateQuietly(array $attributes = [])
     * @method _IH_Calendario_C|Calendario[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Calendario_C|Calendario[] get(array|string $columns = ['*'])
     * @method Calendario getModel()
     * @method Calendario[] getModels(array|string $columns = ['*'])
     * @method _IH_Calendario_C|Calendario[] hydrate(array $items)
     * @method Calendario make(array $attributes = [])
     * @method Calendario newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Calendario[]|_IH_Calendario_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Calendario restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Calendario[]|_IH_Calendario_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Calendario sole(array|string $columns = ['*'])
     * @method Calendario updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Calendario_QB extends _BaseBuilder {}
    
    /**
     * @method LiveChat|null getOrPut($key, $value)
     * @method LiveChat|$this shift(int $count = 1)
     * @method LiveChat|null firstOrFail($key = null, $operator = null, $value = null)
     * @method LiveChat|$this pop(int $count = 1)
     * @method LiveChat|null pull($key, $default = null)
     * @method LiveChat|null last(callable $callback = null, $default = null)
     * @method LiveChat|$this random($number = null, bool $preserveKeys = false)
     * @method LiveChat|null sole($key = null, $operator = null, $value = null)
     * @method LiveChat|null get($key, $default = null)
     * @method LiveChat|null first(callable $callback = null, $default = null)
     * @method LiveChat|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method LiveChat|null find($key, $default = null)
     * @method LiveChat[] all()
     */
    class _IH_LiveChat_C extends _BaseCollection {
        /**
         * @param int $size
         * @return LiveChat[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method LiveChat baseSole(array|string $columns = ['*'])
     * @method LiveChat create(array $attributes = [])
     * @method LiveChat createOrFirst(array $attributes = [], array $values = [])
     * @method LiveChat createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_LiveChat_C|LiveChat[] cursor()
     * @method LiveChat|null|_IH_LiveChat_C|LiveChat[] find($id, array|string $columns = ['*'])
     * @method _IH_LiveChat_C|LiveChat[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method LiveChat|_IH_LiveChat_C|LiveChat[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method LiveChat|_IH_LiveChat_C|LiveChat[] findOrFail($id, array|string $columns = ['*'])
     * @method LiveChat|_IH_LiveChat_C|LiveChat[] findOrNew($id, array|string $columns = ['*'])
     * @method LiveChat first(array|string $columns = ['*'])
     * @method LiveChat firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method LiveChat firstOrCreate(array $attributes = [], array $values = [])
     * @method LiveChat firstOrFail(array|string $columns = ['*'])
     * @method LiveChat firstOrNew(array $attributes = [], array $values = [])
     * @method LiveChat firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method LiveChat forceCreate(array $attributes)
     * @method LiveChat forceCreateQuietly(array $attributes = [])
     * @method _IH_LiveChat_C|LiveChat[] fromQuery(string $query, array $bindings = [])
     * @method _IH_LiveChat_C|LiveChat[] get(array|string $columns = ['*'])
     * @method LiveChat getModel()
     * @method LiveChat[] getModels(array|string $columns = ['*'])
     * @method _IH_LiveChat_C|LiveChat[] hydrate(array $items)
     * @method LiveChat make(array $attributes = [])
     * @method LiveChat newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|LiveChat[]|_IH_LiveChat_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method LiveChat restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|LiveChat[]|_IH_LiveChat_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method LiveChat sole(array|string $columns = ['*'])
     * @method LiveChat updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_LiveChat_QB extends _BaseBuilder {}
    
    /**
     * @method MatchCommentary|null getOrPut($key, $value)
     * @method MatchCommentary|$this shift(int $count = 1)
     * @method MatchCommentary|null firstOrFail($key = null, $operator = null, $value = null)
     * @method MatchCommentary|$this pop(int $count = 1)
     * @method MatchCommentary|null pull($key, $default = null)
     * @method MatchCommentary|null last(callable $callback = null, $default = null)
     * @method MatchCommentary|$this random($number = null, bool $preserveKeys = false)
     * @method MatchCommentary|null sole($key = null, $operator = null, $value = null)
     * @method MatchCommentary|null get($key, $default = null)
     * @method MatchCommentary|null first(callable $callback = null, $default = null)
     * @method MatchCommentary|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method MatchCommentary|null find($key, $default = null)
     * @method MatchCommentary[] all()
     */
    class _IH_MatchCommentary_C extends _BaseCollection {
        /**
         * @param int $size
         * @return MatchCommentary[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method MatchCommentary baseSole(array|string $columns = ['*'])
     * @method MatchCommentary create(array $attributes = [])
     * @method MatchCommentary createOrFirst(array $attributes = [], array $values = [])
     * @method MatchCommentary createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_MatchCommentary_C|MatchCommentary[] cursor()
     * @method MatchCommentary|null|_IH_MatchCommentary_C|MatchCommentary[] find($id, array|string $columns = ['*'])
     * @method _IH_MatchCommentary_C|MatchCommentary[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method MatchCommentary|_IH_MatchCommentary_C|MatchCommentary[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchCommentary|_IH_MatchCommentary_C|MatchCommentary[] findOrFail($id, array|string $columns = ['*'])
     * @method MatchCommentary|_IH_MatchCommentary_C|MatchCommentary[] findOrNew($id, array|string $columns = ['*'])
     * @method MatchCommentary first(array|string $columns = ['*'])
     * @method MatchCommentary firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchCommentary firstOrCreate(array $attributes = [], array $values = [])
     * @method MatchCommentary firstOrFail(array|string $columns = ['*'])
     * @method MatchCommentary firstOrNew(array $attributes = [], array $values = [])
     * @method MatchCommentary firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method MatchCommentary forceCreate(array $attributes)
     * @method MatchCommentary forceCreateQuietly(array $attributes = [])
     * @method _IH_MatchCommentary_C|MatchCommentary[] fromQuery(string $query, array $bindings = [])
     * @method _IH_MatchCommentary_C|MatchCommentary[] get(array|string $columns = ['*'])
     * @method MatchCommentary getModel()
     * @method MatchCommentary[] getModels(array|string $columns = ['*'])
     * @method _IH_MatchCommentary_C|MatchCommentary[] hydrate(array $items)
     * @method MatchCommentary make(array $attributes = [])
     * @method MatchCommentary newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|MatchCommentary[]|_IH_MatchCommentary_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchCommentary restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|MatchCommentary[]|_IH_MatchCommentary_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchCommentary sole(array|string $columns = ['*'])
     * @method MatchCommentary updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_MatchCommentary_QB extends _BaseBuilder {}
    
    /**
     * @method MatchLineups|null getOrPut($key, $value)
     * @method MatchLineups|$this shift(int $count = 1)
     * @method MatchLineups|null firstOrFail($key = null, $operator = null, $value = null)
     * @method MatchLineups|$this pop(int $count = 1)
     * @method MatchLineups|null pull($key, $default = null)
     * @method MatchLineups|null last(callable $callback = null, $default = null)
     * @method MatchLineups|$this random($number = null, bool $preserveKeys = false)
     * @method MatchLineups|null sole($key = null, $operator = null, $value = null)
     * @method MatchLineups|null get($key, $default = null)
     * @method MatchLineups|null first(callable $callback = null, $default = null)
     * @method MatchLineups|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method MatchLineups|null find($key, $default = null)
     * @method MatchLineups[] all()
     */
    class _IH_MatchLineups_C extends _BaseCollection {
        /**
         * @param int $size
         * @return MatchLineups[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method MatchLineups baseSole(array|string $columns = ['*'])
     * @method MatchLineups create(array $attributes = [])
     * @method MatchLineups createOrFirst(array $attributes = [], array $values = [])
     * @method MatchLineups createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_MatchLineups_C|MatchLineups[] cursor()
     * @method MatchLineups|null|_IH_MatchLineups_C|MatchLineups[] find($id, array|string $columns = ['*'])
     * @method _IH_MatchLineups_C|MatchLineups[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method MatchLineups|_IH_MatchLineups_C|MatchLineups[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchLineups|_IH_MatchLineups_C|MatchLineups[] findOrFail($id, array|string $columns = ['*'])
     * @method MatchLineups|_IH_MatchLineups_C|MatchLineups[] findOrNew($id, array|string $columns = ['*'])
     * @method MatchLineups first(array|string $columns = ['*'])
     * @method MatchLineups firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchLineups firstOrCreate(array $attributes = [], array $values = [])
     * @method MatchLineups firstOrFail(array|string $columns = ['*'])
     * @method MatchLineups firstOrNew(array $attributes = [], array $values = [])
     * @method MatchLineups firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method MatchLineups forceCreate(array $attributes)
     * @method MatchLineups forceCreateQuietly(array $attributes = [])
     * @method _IH_MatchLineups_C|MatchLineups[] fromQuery(string $query, array $bindings = [])
     * @method _IH_MatchLineups_C|MatchLineups[] get(array|string $columns = ['*'])
     * @method MatchLineups getModel()
     * @method MatchLineups[] getModels(array|string $columns = ['*'])
     * @method _IH_MatchLineups_C|MatchLineups[] hydrate(array $items)
     * @method MatchLineups make(array $attributes = [])
     * @method MatchLineups newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|MatchLineups[]|_IH_MatchLineups_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchLineups restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|MatchLineups[]|_IH_MatchLineups_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchLineups sole(array|string $columns = ['*'])
     * @method MatchLineups updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_MatchLineups_QB extends _BaseBuilder {}
    
    /**
     * @method MatchStatics|null getOrPut($key, $value)
     * @method MatchStatics|$this shift(int $count = 1)
     * @method MatchStatics|null firstOrFail($key = null, $operator = null, $value = null)
     * @method MatchStatics|$this pop(int $count = 1)
     * @method MatchStatics|null pull($key, $default = null)
     * @method MatchStatics|null last(callable $callback = null, $default = null)
     * @method MatchStatics|$this random($number = null, bool $preserveKeys = false)
     * @method MatchStatics|null sole($key = null, $operator = null, $value = null)
     * @method MatchStatics|null get($key, $default = null)
     * @method MatchStatics|null first(callable $callback = null, $default = null)
     * @method MatchStatics|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method MatchStatics|null find($key, $default = null)
     * @method MatchStatics[] all()
     */
    class _IH_MatchStatics_C extends _BaseCollection {
        /**
         * @param int $size
         * @return MatchStatics[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method MatchStatics baseSole(array|string $columns = ['*'])
     * @method MatchStatics create(array $attributes = [])
     * @method MatchStatics createOrFirst(array $attributes = [], array $values = [])
     * @method MatchStatics createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_MatchStatics_C|MatchStatics[] cursor()
     * @method MatchStatics|null|_IH_MatchStatics_C|MatchStatics[] find($id, array|string $columns = ['*'])
     * @method _IH_MatchStatics_C|MatchStatics[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method MatchStatics|_IH_MatchStatics_C|MatchStatics[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchStatics|_IH_MatchStatics_C|MatchStatics[] findOrFail($id, array|string $columns = ['*'])
     * @method MatchStatics|_IH_MatchStatics_C|MatchStatics[] findOrNew($id, array|string $columns = ['*'])
     * @method MatchStatics first(array|string $columns = ['*'])
     * @method MatchStatics firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchStatics firstOrCreate(array $attributes = [], array $values = [])
     * @method MatchStatics firstOrFail(array|string $columns = ['*'])
     * @method MatchStatics firstOrNew(array $attributes = [], array $values = [])
     * @method MatchStatics firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method MatchStatics forceCreate(array $attributes)
     * @method MatchStatics forceCreateQuietly(array $attributes = [])
     * @method _IH_MatchStatics_C|MatchStatics[] fromQuery(string $query, array $bindings = [])
     * @method _IH_MatchStatics_C|MatchStatics[] get(array|string $columns = ['*'])
     * @method MatchStatics getModel()
     * @method MatchStatics[] getModels(array|string $columns = ['*'])
     * @method _IH_MatchStatics_C|MatchStatics[] hydrate(array $items)
     * @method MatchStatics make(array $attributes = [])
     * @method MatchStatics newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|MatchStatics[]|_IH_MatchStatics_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchStatics restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|MatchStatics[]|_IH_MatchStatics_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchStatics sole(array|string $columns = ['*'])
     * @method MatchStatics updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_MatchStatics_QB extends _BaseBuilder {}
    
    /**
     * @method MatchSummary|null getOrPut($key, $value)
     * @method MatchSummary|$this shift(int $count = 1)
     * @method MatchSummary|null firstOrFail($key = null, $operator = null, $value = null)
     * @method MatchSummary|$this pop(int $count = 1)
     * @method MatchSummary|null pull($key, $default = null)
     * @method MatchSummary|null last(callable $callback = null, $default = null)
     * @method MatchSummary|$this random($number = null, bool $preserveKeys = false)
     * @method MatchSummary|null sole($key = null, $operator = null, $value = null)
     * @method MatchSummary|null get($key, $default = null)
     * @method MatchSummary|null first(callable $callback = null, $default = null)
     * @method MatchSummary|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method MatchSummary|null find($key, $default = null)
     * @method MatchSummary[] all()
     */
    class _IH_MatchSummary_C extends _BaseCollection {
        /**
         * @param int $size
         * @return MatchSummary[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method MatchSummary baseSole(array|string $columns = ['*'])
     * @method MatchSummary create(array $attributes = [])
     * @method MatchSummary createOrFirst(array $attributes = [], array $values = [])
     * @method MatchSummary createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_MatchSummary_C|MatchSummary[] cursor()
     * @method MatchSummary|null|_IH_MatchSummary_C|MatchSummary[] find($id, array|string $columns = ['*'])
     * @method _IH_MatchSummary_C|MatchSummary[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method MatchSummary|_IH_MatchSummary_C|MatchSummary[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchSummary|_IH_MatchSummary_C|MatchSummary[] findOrFail($id, array|string $columns = ['*'])
     * @method MatchSummary|_IH_MatchSummary_C|MatchSummary[] findOrNew($id, array|string $columns = ['*'])
     * @method MatchSummary first(array|string $columns = ['*'])
     * @method MatchSummary firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method MatchSummary firstOrCreate(array $attributes = [], array $values = [])
     * @method MatchSummary firstOrFail(array|string $columns = ['*'])
     * @method MatchSummary firstOrNew(array $attributes = [], array $values = [])
     * @method MatchSummary firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method MatchSummary forceCreate(array $attributes)
     * @method MatchSummary forceCreateQuietly(array $attributes = [])
     * @method _IH_MatchSummary_C|MatchSummary[] fromQuery(string $query, array $bindings = [])
     * @method _IH_MatchSummary_C|MatchSummary[] get(array|string $columns = ['*'])
     * @method MatchSummary getModel()
     * @method MatchSummary[] getModels(array|string $columns = ['*'])
     * @method _IH_MatchSummary_C|MatchSummary[] hydrate(array $items)
     * @method MatchSummary make(array $attributes = [])
     * @method MatchSummary newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|MatchSummary[]|_IH_MatchSummary_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchSummary restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|MatchSummary[]|_IH_MatchSummary_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method MatchSummary sole(array|string $columns = ['*'])
     * @method MatchSummary updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_MatchSummary_QB extends _BaseBuilder {}
    
    /**
     * @method Matches|null getOrPut($key, $value)
     * @method Matches|$this shift(int $count = 1)
     * @method Matches|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Matches|$this pop(int $count = 1)
     * @method Matches|null pull($key, $default = null)
     * @method Matches|null last(callable $callback = null, $default = null)
     * @method Matches|$this random($number = null, bool $preserveKeys = false)
     * @method Matches|null sole($key = null, $operator = null, $value = null)
     * @method Matches|null get($key, $default = null)
     * @method Matches|null first(callable $callback = null, $default = null)
     * @method Matches|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Matches|null find($key, $default = null)
     * @method Matches[] all()
     */
    class _IH_Matches_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Matches[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Matches_QB whereId($value)
     * @method _IH_Matches_QB whereMatchId($value)
     * @method _IH_Matches_QB whereVenue($value)
     * @method _IH_Matches_QB whereMatchday($value)
     * @method _IH_Matches_QB whereStage($value)
     * @method _IH_Matches_QB whereGroup($value)
     * @method _IH_Matches_QB whereMatchDate($value)
     * @method _IH_Matches_QB whereStatus($value)
     * @method _IH_Matches_QB whereHomeTeam($value)
     * @method _IH_Matches_QB whereAwayTeam($value)
     * @method _IH_Matches_QB whereScore($value)
     * @method _IH_Matches_QB whereGoals($value)
     * @method _IH_Matches_QB wherePenalties($value)
     * @method _IH_Matches_QB whereBookings($value)
     * @method _IH_Matches_QB whereSubstitutions($value)
     * @method _IH_Matches_QB whereOdds($value)
     * @method _IH_Matches_QB whereReferees($value)
     * @method _IH_Matches_QB whereCreatedAt($value)
     * @method _IH_Matches_QB whereUpdatedAt($value)
     * @method Matches baseSole(array|string $columns = ['*'])
     * @method Matches create(array $attributes = [])
     * @method Matches createOrFirst(array $attributes = [], array $values = [])
     * @method Matches createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Matches_C|Matches[] cursor()
     * @method Matches|null|_IH_Matches_C|Matches[] find($id, array|string $columns = ['*'])
     * @method _IH_Matches_C|Matches[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Matches|_IH_Matches_C|Matches[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Matches|_IH_Matches_C|Matches[] findOrFail($id, array|string $columns = ['*'])
     * @method Matches|_IH_Matches_C|Matches[] findOrNew($id, array|string $columns = ['*'])
     * @method Matches first(array|string $columns = ['*'])
     * @method Matches firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Matches firstOrCreate(array $attributes = [], array $values = [])
     * @method Matches firstOrFail(array|string $columns = ['*'])
     * @method Matches firstOrNew(array $attributes = [], array $values = [])
     * @method Matches firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Matches forceCreate(array $attributes)
     * @method Matches forceCreateQuietly(array $attributes = [])
     * @method _IH_Matches_C|Matches[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Matches_C|Matches[] get(array|string $columns = ['*'])
     * @method Matches getModel()
     * @method Matches[] getModels(array|string $columns = ['*'])
     * @method _IH_Matches_C|Matches[] hydrate(array $items)
     * @method Matches make(array $attributes = [])
     * @method Matches newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Matches[]|_IH_Matches_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Matches restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Matches[]|_IH_Matches_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Matches sole(array|string $columns = ['*'])
     * @method Matches updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Matches_QB extends _BaseBuilder {}
    
    /**
     * @method Message|null getOrPut($key, $value)
     * @method Message|$this shift(int $count = 1)
     * @method Message|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Message|$this pop(int $count = 1)
     * @method Message|null pull($key, $default = null)
     * @method Message|null last(callable $callback = null, $default = null)
     * @method Message|$this random($number = null, bool $preserveKeys = false)
     * @method Message|null sole($key = null, $operator = null, $value = null)
     * @method Message|null get($key, $default = null)
     * @method Message|null first(callable $callback = null, $default = null)
     * @method Message|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Message|null find($key, $default = null)
     * @method Message[] all()
     */
    class _IH_Message_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Message[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Message baseSole(array|string $columns = ['*'])
     * @method Message create(array $attributes = [])
     * @method Message createOrFirst(array $attributes = [], array $values = [])
     * @method Message createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Message_C|Message[] cursor()
     * @method Message|null|_IH_Message_C|Message[] find($id, array|string $columns = ['*'])
     * @method _IH_Message_C|Message[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Message|_IH_Message_C|Message[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Message|_IH_Message_C|Message[] findOrFail($id, array|string $columns = ['*'])
     * @method Message|_IH_Message_C|Message[] findOrNew($id, array|string $columns = ['*'])
     * @method Message first(array|string $columns = ['*'])
     * @method Message firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Message firstOrCreate(array $attributes = [], array $values = [])
     * @method Message firstOrFail(array|string $columns = ['*'])
     * @method Message firstOrNew(array $attributes = [], array $values = [])
     * @method Message firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Message forceCreate(array $attributes)
     * @method Message forceCreateQuietly(array $attributes = [])
     * @method _IH_Message_C|Message[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Message_C|Message[] get(array|string $columns = ['*'])
     * @method Message getModel()
     * @method Message[] getModels(array|string $columns = ['*'])
     * @method _IH_Message_C|Message[] hydrate(array $items)
     * @method Message make(array $attributes = [])
     * @method Message newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Message[]|_IH_Message_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Message restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Message[]|_IH_Message_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Message sole(array|string $columns = ['*'])
     * @method Message updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Message_QB extends _BaseBuilder {}
    
    /**
     * @method Notifica|null getOrPut($key, $value)
     * @method Notifica|$this shift(int $count = 1)
     * @method Notifica|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Notifica|$this pop(int $count = 1)
     * @method Notifica|null pull($key, $default = null)
     * @method Notifica|null last(callable $callback = null, $default = null)
     * @method Notifica|$this random($number = null, bool $preserveKeys = false)
     * @method Notifica|null sole($key = null, $operator = null, $value = null)
     * @method Notifica|null get($key, $default = null)
     * @method Notifica|null first(callable $callback = null, $default = null)
     * @method Notifica|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Notifica|null find($key, $default = null)
     * @method Notifica[] all()
     */
    class _IH_Notifica_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Notifica[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Notifica baseSole(array|string $columns = ['*'])
     * @method Notifica create(array $attributes = [])
     * @method Notifica createOrFirst(array $attributes = [], array $values = [])
     * @method Notifica createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Notifica_C|Notifica[] cursor()
     * @method Notifica|null|_IH_Notifica_C|Notifica[] find($id, array|string $columns = ['*'])
     * @method _IH_Notifica_C|Notifica[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Notifica|_IH_Notifica_C|Notifica[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Notifica|_IH_Notifica_C|Notifica[] findOrFail($id, array|string $columns = ['*'])
     * @method Notifica|_IH_Notifica_C|Notifica[] findOrNew($id, array|string $columns = ['*'])
     * @method Notifica first(array|string $columns = ['*'])
     * @method Notifica firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Notifica firstOrCreate(array $attributes = [], array $values = [])
     * @method Notifica firstOrFail(array|string $columns = ['*'])
     * @method Notifica firstOrNew(array $attributes = [], array $values = [])
     * @method Notifica firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Notifica forceCreate(array $attributes)
     * @method Notifica forceCreateQuietly(array $attributes = [])
     * @method _IH_Notifica_C|Notifica[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Notifica_C|Notifica[] get(array|string $columns = ['*'])
     * @method Notifica getModel()
     * @method Notifica[] getModels(array|string $columns = ['*'])
     * @method _IH_Notifica_C|Notifica[] hydrate(array $items)
     * @method Notifica make(array $attributes = [])
     * @method Notifica newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Notifica[]|_IH_Notifica_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Notifica restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Notifica[]|_IH_Notifica_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Notifica sole(array|string $columns = ['*'])
     * @method Notifica updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Notifica_QB extends _BaseBuilder {}
    
    /**
     * @method PlayerStats|null getOrPut($key, $value)
     * @method PlayerStats|$this shift(int $count = 1)
     * @method PlayerStats|null firstOrFail($key = null, $operator = null, $value = null)
     * @method PlayerStats|$this pop(int $count = 1)
     * @method PlayerStats|null pull($key, $default = null)
     * @method PlayerStats|null last(callable $callback = null, $default = null)
     * @method PlayerStats|$this random($number = null, bool $preserveKeys = false)
     * @method PlayerStats|null sole($key = null, $operator = null, $value = null)
     * @method PlayerStats|null get($key, $default = null)
     * @method PlayerStats|null first(callable $callback = null, $default = null)
     * @method PlayerStats|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PlayerStats|null find($key, $default = null)
     * @method PlayerStats[] all()
     */
    class _IH_PlayerStats_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PlayerStats[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method PlayerStats baseSole(array|string $columns = ['*'])
     * @method PlayerStats create(array $attributes = [])
     * @method PlayerStats createOrFirst(array $attributes = [], array $values = [])
     * @method PlayerStats createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_PlayerStats_C|PlayerStats[] cursor()
     * @method PlayerStats|null|_IH_PlayerStats_C|PlayerStats[] find($id, array|string $columns = ['*'])
     * @method _IH_PlayerStats_C|PlayerStats[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PlayerStats|_IH_PlayerStats_C|PlayerStats[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PlayerStats|_IH_PlayerStats_C|PlayerStats[] findOrFail($id, array|string $columns = ['*'])
     * @method PlayerStats|_IH_PlayerStats_C|PlayerStats[] findOrNew($id, array|string $columns = ['*'])
     * @method PlayerStats first(array|string $columns = ['*'])
     * @method PlayerStats firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PlayerStats firstOrCreate(array $attributes = [], array $values = [])
     * @method PlayerStats firstOrFail(array|string $columns = ['*'])
     * @method PlayerStats firstOrNew(array $attributes = [], array $values = [])
     * @method PlayerStats firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PlayerStats forceCreate(array $attributes)
     * @method PlayerStats forceCreateQuietly(array $attributes = [])
     * @method _IH_PlayerStats_C|PlayerStats[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PlayerStats_C|PlayerStats[] get(array|string $columns = ['*'])
     * @method PlayerStats getModel()
     * @method PlayerStats[] getModels(array|string $columns = ['*'])
     * @method _IH_PlayerStats_C|PlayerStats[] hydrate(array $items)
     * @method PlayerStats make(array $attributes = [])
     * @method PlayerStats newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PlayerStats[]|_IH_PlayerStats_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PlayerStats restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|PlayerStats[]|_IH_PlayerStats_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PlayerStats sole(array|string $columns = ['*'])
     * @method PlayerStats updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PlayerStats_QB extends _BaseBuilder {}
    
    /**
     * @method PlayerVotes|null getOrPut($key, $value)
     * @method PlayerVotes|$this shift(int $count = 1)
     * @method PlayerVotes|null firstOrFail($key = null, $operator = null, $value = null)
     * @method PlayerVotes|$this pop(int $count = 1)
     * @method PlayerVotes|null pull($key, $default = null)
     * @method PlayerVotes|null last(callable $callback = null, $default = null)
     * @method PlayerVotes|$this random($number = null, bool $preserveKeys = false)
     * @method PlayerVotes|null sole($key = null, $operator = null, $value = null)
     * @method PlayerVotes|null get($key, $default = null)
     * @method PlayerVotes|null first(callable $callback = null, $default = null)
     * @method PlayerVotes|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PlayerVotes|null find($key, $default = null)
     * @method PlayerVotes[] all()
     */
    class _IH_PlayerVotes_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PlayerVotes[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method PlayerVotes baseSole(array|string $columns = ['*'])
     * @method PlayerVotes create(array $attributes = [])
     * @method PlayerVotes createOrFirst(array $attributes = [], array $values = [])
     * @method PlayerVotes createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_PlayerVotes_C|PlayerVotes[] cursor()
     * @method PlayerVotes|null|_IH_PlayerVotes_C|PlayerVotes[] find($id, array|string $columns = ['*'])
     * @method _IH_PlayerVotes_C|PlayerVotes[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PlayerVotes|_IH_PlayerVotes_C|PlayerVotes[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PlayerVotes|_IH_PlayerVotes_C|PlayerVotes[] findOrFail($id, array|string $columns = ['*'])
     * @method PlayerVotes|_IH_PlayerVotes_C|PlayerVotes[] findOrNew($id, array|string $columns = ['*'])
     * @method PlayerVotes first(array|string $columns = ['*'])
     * @method PlayerVotes firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PlayerVotes firstOrCreate(array $attributes = [], array $values = [])
     * @method PlayerVotes firstOrFail(array|string $columns = ['*'])
     * @method PlayerVotes firstOrNew(array $attributes = [], array $values = [])
     * @method PlayerVotes firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PlayerVotes forceCreate(array $attributes)
     * @method PlayerVotes forceCreateQuietly(array $attributes = [])
     * @method _IH_PlayerVotes_C|PlayerVotes[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PlayerVotes_C|PlayerVotes[] get(array|string $columns = ['*'])
     * @method PlayerVotes getModel()
     * @method PlayerVotes[] getModels(array|string $columns = ['*'])
     * @method _IH_PlayerVotes_C|PlayerVotes[] hydrate(array $items)
     * @method PlayerVotes make(array $attributes = [])
     * @method PlayerVotes newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PlayerVotes[]|_IH_PlayerVotes_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PlayerVotes restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|PlayerVotes[]|_IH_PlayerVotes_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PlayerVotes sole(array|string $columns = ['*'])
     * @method PlayerVotes updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PlayerVotes_QB extends _BaseBuilder {}
    
    /**
     * @method Player|null getOrPut($key, $value)
     * @method Player|$this shift(int $count = 1)
     * @method Player|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Player|$this pop(int $count = 1)
     * @method Player|null pull($key, $default = null)
     * @method Player|null last(callable $callback = null, $default = null)
     * @method Player|$this random($number = null, bool $preserveKeys = false)
     * @method Player|null sole($key = null, $operator = null, $value = null)
     * @method Player|null get($key, $default = null)
     * @method Player|null first(callable $callback = null, $default = null)
     * @method Player|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Player|null find($key, $default = null)
     * @method Player[] all()
     */
    class _IH_Player_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Player[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Player baseSole(array|string $columns = ['*'])
     * @method Player create(array $attributes = [])
     * @method Player createOrFirst(array $attributes = [], array $values = [])
     * @method Player createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Player_C|Player[] cursor()
     * @method Player|null|_IH_Player_C|Player[] find($id, array|string $columns = ['*'])
     * @method _IH_Player_C|Player[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Player|_IH_Player_C|Player[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Player|_IH_Player_C|Player[] findOrFail($id, array|string $columns = ['*'])
     * @method Player|_IH_Player_C|Player[] findOrNew($id, array|string $columns = ['*'])
     * @method Player first(array|string $columns = ['*'])
     * @method Player firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Player firstOrCreate(array $attributes = [], array $values = [])
     * @method Player firstOrFail(array|string $columns = ['*'])
     * @method Player firstOrNew(array $attributes = [], array $values = [])
     * @method Player firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Player forceCreate(array $attributes)
     * @method Player forceCreateQuietly(array $attributes = [])
     * @method _IH_Player_C|Player[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Player_C|Player[] get(array|string $columns = ['*'])
     * @method Player getModel()
     * @method Player[] getModels(array|string $columns = ['*'])
     * @method _IH_Player_C|Player[] hydrate(array $items)
     * @method Player make(array $attributes = [])
     * @method Player newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Player[]|_IH_Player_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Player restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Player[]|_IH_Player_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Player sole(array|string $columns = ['*'])
     * @method Player updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Player_QB extends _BaseBuilder {}
    
    /**
     * @method PollOption|null getOrPut($key, $value)
     * @method PollOption|$this shift(int $count = 1)
     * @method PollOption|null firstOrFail($key = null, $operator = null, $value = null)
     * @method PollOption|$this pop(int $count = 1)
     * @method PollOption|null pull($key, $default = null)
     * @method PollOption|null last(callable $callback = null, $default = null)
     * @method PollOption|$this random($number = null, bool $preserveKeys = false)
     * @method PollOption|null sole($key = null, $operator = null, $value = null)
     * @method PollOption|null get($key, $default = null)
     * @method PollOption|null first(callable $callback = null, $default = null)
     * @method PollOption|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PollOption|null find($key, $default = null)
     * @method PollOption[] all()
     */
    class _IH_PollOption_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PollOption[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method PollOption baseSole(array|string $columns = ['*'])
     * @method PollOption create(array $attributes = [])
     * @method PollOption createOrFirst(array $attributes = [], array $values = [])
     * @method PollOption createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_PollOption_C|PollOption[] cursor()
     * @method PollOption|null|_IH_PollOption_C|PollOption[] find($id, array|string $columns = ['*'])
     * @method _IH_PollOption_C|PollOption[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PollOption|_IH_PollOption_C|PollOption[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PollOption|_IH_PollOption_C|PollOption[] findOrFail($id, array|string $columns = ['*'])
     * @method PollOption|_IH_PollOption_C|PollOption[] findOrNew($id, array|string $columns = ['*'])
     * @method PollOption first(array|string $columns = ['*'])
     * @method PollOption firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PollOption firstOrCreate(array $attributes = [], array $values = [])
     * @method PollOption firstOrFail(array|string $columns = ['*'])
     * @method PollOption firstOrNew(array $attributes = [], array $values = [])
     * @method PollOption firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PollOption forceCreate(array $attributes)
     * @method PollOption forceCreateQuietly(array $attributes = [])
     * @method _IH_PollOption_C|PollOption[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PollOption_C|PollOption[] get(array|string $columns = ['*'])
     * @method PollOption getModel()
     * @method PollOption[] getModels(array|string $columns = ['*'])
     * @method _IH_PollOption_C|PollOption[] hydrate(array $items)
     * @method PollOption make(array $attributes = [])
     * @method PollOption newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PollOption[]|_IH_PollOption_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PollOption restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|PollOption[]|_IH_PollOption_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PollOption sole(array|string $columns = ['*'])
     * @method PollOption updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PollOption_QB extends _BaseBuilder {}
    
    /**
     * @method Poll|null getOrPut($key, $value)
     * @method Poll|$this shift(int $count = 1)
     * @method Poll|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Poll|$this pop(int $count = 1)
     * @method Poll|null pull($key, $default = null)
     * @method Poll|null last(callable $callback = null, $default = null)
     * @method Poll|$this random($number = null, bool $preserveKeys = false)
     * @method Poll|null sole($key = null, $operator = null, $value = null)
     * @method Poll|null get($key, $default = null)
     * @method Poll|null first(callable $callback = null, $default = null)
     * @method Poll|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Poll|null find($key, $default = null)
     * @method Poll[] all()
     */
    class _IH_Poll_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Poll[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Poll baseSole(array|string $columns = ['*'])
     * @method Poll create(array $attributes = [])
     * @method Poll createOrFirst(array $attributes = [], array $values = [])
     * @method Poll createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Poll_C|Poll[] cursor()
     * @method Poll|null|_IH_Poll_C|Poll[] find($id, array|string $columns = ['*'])
     * @method _IH_Poll_C|Poll[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Poll|_IH_Poll_C|Poll[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Poll|_IH_Poll_C|Poll[] findOrFail($id, array|string $columns = ['*'])
     * @method Poll|_IH_Poll_C|Poll[] findOrNew($id, array|string $columns = ['*'])
     * @method Poll first(array|string $columns = ['*'])
     * @method Poll firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Poll firstOrCreate(array $attributes = [], array $values = [])
     * @method Poll firstOrFail(array|string $columns = ['*'])
     * @method Poll firstOrNew(array $attributes = [], array $values = [])
     * @method Poll firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Poll forceCreate(array $attributes)
     * @method Poll forceCreateQuietly(array $attributes = [])
     * @method _IH_Poll_C|Poll[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Poll_C|Poll[] get(array|string $columns = ['*'])
     * @method Poll getModel()
     * @method Poll[] getModels(array|string $columns = ['*'])
     * @method _IH_Poll_C|Poll[] hydrate(array $items)
     * @method Poll make(array $attributes = [])
     * @method Poll newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Poll[]|_IH_Poll_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Poll restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Poll[]|_IH_Poll_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Poll sole(array|string $columns = ['*'])
     * @method Poll updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Poll_QB extends _BaseBuilder {}
    
    /**
     * @method Standing|null getOrPut($key, $value)
     * @method Standing|$this shift(int $count = 1)
     * @method Standing|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Standing|$this pop(int $count = 1)
     * @method Standing|null pull($key, $default = null)
     * @method Standing|null last(callable $callback = null, $default = null)
     * @method Standing|$this random($number = null, bool $preserveKeys = false)
     * @method Standing|null sole($key = null, $operator = null, $value = null)
     * @method Standing|null get($key, $default = null)
     * @method Standing|null first(callable $callback = null, $default = null)
     * @method Standing|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Standing|null find($key, $default = null)
     * @method Standing[] all()
     */
    class _IH_Standing_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Standing[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Standing_QB whereId($value)
     * @method _IH_Standing_QB wherePosition($value)
     * @method _IH_Standing_QB whereTeamId($value)
     * @method _IH_Standing_QB whereTeamName($value)
     * @method _IH_Standing_QB whereShortName($value)
     * @method _IH_Standing_QB whereTla($value)
     * @method _IH_Standing_QB whereCrestUrl($value)
     * @method _IH_Standing_QB wherePlayedGames($value)
     * @method _IH_Standing_QB whereForm($value)
     * @method _IH_Standing_QB whereWon($value)
     * @method _IH_Standing_QB whereDraw($value)
     * @method _IH_Standing_QB whereLost($value)
     * @method _IH_Standing_QB wherePoints($value)
     * @method _IH_Standing_QB whereGoalsFor($value)
     * @method _IH_Standing_QB whereGoalsAgainst($value)
     * @method _IH_Standing_QB whereGoalDifference($value)
     * @method _IH_Standing_QB whereCreatedAt($value)
     * @method _IH_Standing_QB whereUpdatedAt($value)
     * @method Standing baseSole(array|string $columns = ['*'])
     * @method Standing create(array $attributes = [])
     * @method Standing createOrFirst(array $attributes = [], array $values = [])
     * @method Standing createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Standing_C|Standing[] cursor()
     * @method Standing|null|_IH_Standing_C|Standing[] find($id, array|string $columns = ['*'])
     * @method _IH_Standing_C|Standing[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Standing|_IH_Standing_C|Standing[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Standing|_IH_Standing_C|Standing[] findOrFail($id, array|string $columns = ['*'])
     * @method Standing|_IH_Standing_C|Standing[] findOrNew($id, array|string $columns = ['*'])
     * @method Standing first(array|string $columns = ['*'])
     * @method Standing firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Standing firstOrCreate(array $attributes = [], array $values = [])
     * @method Standing firstOrFail(array|string $columns = ['*'])
     * @method Standing firstOrNew(array $attributes = [], array $values = [])
     * @method Standing firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Standing forceCreate(array $attributes)
     * @method Standing forceCreateQuietly(array $attributes = [])
     * @method _IH_Standing_C|Standing[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Standing_C|Standing[] get(array|string $columns = ['*'])
     * @method Standing getModel()
     * @method Standing[] getModels(array|string $columns = ['*'])
     * @method _IH_Standing_C|Standing[] hydrate(array $items)
     * @method Standing make(array $attributes = [])
     * @method Standing newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Standing[]|_IH_Standing_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Standing restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Standing[]|_IH_Standing_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Standing sole(array|string $columns = ['*'])
     * @method Standing updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Standing_QB extends _BaseBuilder {}
    
    /**
     * @method User|null getOrPut($key, $value)
     * @method User|$this shift(int $count = 1)
     * @method User|null firstOrFail($key = null, $operator = null, $value = null)
     * @method User|$this pop(int $count = 1)
     * @method User|null pull($key, $default = null)
     * @method User|null last(callable $callback = null, $default = null)
     * @method User|$this random($number = null, bool $preserveKeys = false)
     * @method User|null sole($key = null, $operator = null, $value = null)
     * @method User|null get($key, $default = null)
     * @method User|null first(callable $callback = null, $default = null)
     * @method User|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method User|null find($key, $default = null)
     * @method User[] all()
     */
    class _IH_User_C extends _BaseCollection {
        /**
         * @param int $size
         * @return User[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_User_QB whereId($value)
     * @method _IH_User_QB whereEmail($value)
     * @method _IH_User_QB whereEmailVerifiedAt($value)
     * @method _IH_User_QB wherePassword($value)
     * @method _IH_User_QB whereRememberToken($value)
     * @method _IH_User_QB whereCreatedAt($value)
     * @method _IH_User_QB whereUpdatedAt($value)
     * @method _IH_User_QB whereFirstName($value)
     * @method _IH_User_QB whereLastName($value)
     * @method _IH_User_QB whereUsername($value)
     * @method _IH_User_QB whereAvatarId($value)
     * @method _IH_User_QB whereSuperUser($value)
     * @method _IH_User_QB whereManageSupers($value)
     * @method _IH_User_QB wherePermissions($value)
     * @method _IH_User_QB whereLastLogin($value)
     * @method User baseSole(array|string $columns = ['*'])
     * @method User create(array $attributes = [])
     * @method User createOrFirst(array $attributes = [], array $values = [])
     * @method User createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_User_C|User[] cursor()
     * @method User|null|_IH_User_C|User[] find($id, array|string $columns = ['*'])
     * @method _IH_User_C|User[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method User|_IH_User_C|User[] findOrFail($id, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOrNew($id, array|string $columns = ['*'])
     * @method User first(array|string $columns = ['*'])
     * @method User firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method User firstOrCreate(array $attributes = [], array $values = [])
     * @method User firstOrFail(array|string $columns = ['*'])
     * @method User firstOrNew(array $attributes = [], array $values = [])
     * @method User firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method User forceCreate(array $attributes)
     * @method User forceCreateQuietly(array $attributes = [])
     * @method _IH_User_C|User[] fromQuery(string $query, array $bindings = [])
     * @method _IH_User_C|User[] get(array|string $columns = ['*'])
     * @method User getModel()
     * @method User[] getModels(array|string $columns = ['*'])
     * @method _IH_User_C|User[] hydrate(array $items)
     * @method User make(array $attributes = [])
     * @method User newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|User[]|_IH_User_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method User restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|User[]|_IH_User_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method User sole(array|string $columns = ['*'])
     * @method User updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_User_QB extends _BaseBuilder {}
    
    /**
     * @method VideoAd|null getOrPut($key, $value)
     * @method VideoAd|$this shift(int $count = 1)
     * @method VideoAd|null firstOrFail($key = null, $operator = null, $value = null)
     * @method VideoAd|$this pop(int $count = 1)
     * @method VideoAd|null pull($key, $default = null)
     * @method VideoAd|null last(callable $callback = null, $default = null)
     * @method VideoAd|$this random($number = null, bool $preserveKeys = false)
     * @method VideoAd|null sole($key = null, $operator = null, $value = null)
     * @method VideoAd|null get($key, $default = null)
     * @method VideoAd|null first(callable $callback = null, $default = null)
     * @method VideoAd|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method VideoAd|null find($key, $default = null)
     * @method VideoAd[] all()
     */
    class _IH_VideoAd_C extends _BaseCollection {
        /**
         * @param int $size
         * @return VideoAd[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method VideoAd baseSole(array|string $columns = ['*'])
     * @method VideoAd create(array $attributes = [])
     * @method VideoAd createOrFirst(array $attributes = [], array $values = [])
     * @method VideoAd createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_VideoAd_C|VideoAd[] cursor()
     * @method VideoAd|null|_IH_VideoAd_C|VideoAd[] find($id, array|string $columns = ['*'])
     * @method _IH_VideoAd_C|VideoAd[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method VideoAd|_IH_VideoAd_C|VideoAd[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VideoAd|_IH_VideoAd_C|VideoAd[] findOrFail($id, array|string $columns = ['*'])
     * @method VideoAd|_IH_VideoAd_C|VideoAd[] findOrNew($id, array|string $columns = ['*'])
     * @method VideoAd first(array|string $columns = ['*'])
     * @method VideoAd firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VideoAd firstOrCreate(array $attributes = [], array $values = [])
     * @method VideoAd firstOrFail(array|string $columns = ['*'])
     * @method VideoAd firstOrNew(array $attributes = [], array $values = [])
     * @method VideoAd firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method VideoAd forceCreate(array $attributes)
     * @method VideoAd forceCreateQuietly(array $attributes = [])
     * @method _IH_VideoAd_C|VideoAd[] fromQuery(string $query, array $bindings = [])
     * @method _IH_VideoAd_C|VideoAd[] get(array|string $columns = ['*'])
     * @method VideoAd getModel()
     * @method VideoAd[] getModels(array|string $columns = ['*'])
     * @method _IH_VideoAd_C|VideoAd[] hydrate(array $items)
     * @method VideoAd make(array $attributes = [])
     * @method VideoAd newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|VideoAd[]|_IH_VideoAd_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VideoAd restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|VideoAd[]|_IH_VideoAd_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VideoAd sole(array|string $columns = ['*'])
     * @method VideoAd updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_VideoAd_QB extends _BaseBuilder {}
    
    /**
     * @method VideoSpec|null getOrPut($key, $value)
     * @method VideoSpec|$this shift(int $count = 1)
     * @method VideoSpec|null firstOrFail($key = null, $operator = null, $value = null)
     * @method VideoSpec|$this pop(int $count = 1)
     * @method VideoSpec|null pull($key, $default = null)
     * @method VideoSpec|null last(callable $callback = null, $default = null)
     * @method VideoSpec|$this random($number = null, bool $preserveKeys = false)
     * @method VideoSpec|null sole($key = null, $operator = null, $value = null)
     * @method VideoSpec|null get($key, $default = null)
     * @method VideoSpec|null first(callable $callback = null, $default = null)
     * @method VideoSpec|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method VideoSpec|null find($key, $default = null)
     * @method VideoSpec[] all()
     */
    class _IH_VideoSpec_C extends _BaseCollection {
        /**
         * @param int $size
         * @return VideoSpec[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method VideoSpec baseSole(array|string $columns = ['*'])
     * @method VideoSpec create(array $attributes = [])
     * @method VideoSpec createOrFirst(array $attributes = [], array $values = [])
     * @method VideoSpec createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_VideoSpec_C|VideoSpec[] cursor()
     * @method VideoSpec|null|_IH_VideoSpec_C|VideoSpec[] find($id, array|string $columns = ['*'])
     * @method _IH_VideoSpec_C|VideoSpec[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method VideoSpec|_IH_VideoSpec_C|VideoSpec[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VideoSpec|_IH_VideoSpec_C|VideoSpec[] findOrFail($id, array|string $columns = ['*'])
     * @method VideoSpec|_IH_VideoSpec_C|VideoSpec[] findOrNew($id, array|string $columns = ['*'])
     * @method VideoSpec first(array|string $columns = ['*'])
     * @method VideoSpec firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method VideoSpec firstOrCreate(array $attributes = [], array $values = [])
     * @method VideoSpec firstOrFail(array|string $columns = ['*'])
     * @method VideoSpec firstOrNew(array $attributes = [], array $values = [])
     * @method VideoSpec firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method VideoSpec forceCreate(array $attributes)
     * @method VideoSpec forceCreateQuietly(array $attributes = [])
     * @method _IH_VideoSpec_C|VideoSpec[] fromQuery(string $query, array $bindings = [])
     * @method _IH_VideoSpec_C|VideoSpec[] get(array|string $columns = ['*'])
     * @method VideoSpec getModel()
     * @method VideoSpec[] getModels(array|string $columns = ['*'])
     * @method _IH_VideoSpec_C|VideoSpec[] hydrate(array $items)
     * @method VideoSpec make(array $attributes = [])
     * @method VideoSpec newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|VideoSpec[]|_IH_VideoSpec_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VideoSpec restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|VideoSpec[]|_IH_VideoSpec_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method VideoSpec sole(array|string $columns = ['*'])
     * @method VideoSpec updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_VideoSpec_QB extends _BaseBuilder {}
    
    /**
     * @method Video|null getOrPut($key, $value)
     * @method Video|$this shift(int $count = 1)
     * @method Video|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Video|$this pop(int $count = 1)
     * @method Video|null pull($key, $default = null)
     * @method Video|null last(callable $callback = null, $default = null)
     * @method Video|$this random($number = null, bool $preserveKeys = false)
     * @method Video|null sole($key = null, $operator = null, $value = null)
     * @method Video|null get($key, $default = null)
     * @method Video|null first(callable $callback = null, $default = null)
     * @method Video|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Video|null find($key, $default = null)
     * @method Video[] all()
     */
    class _IH_Video_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Video[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Video_QB whereId($value)
     * @method _IH_Video_QB whereTitle($value)
     * @method _IH_Video_QB whereIsRandom($value)
     * @method _IH_Video_QB wherePublishedAt($value)
     * @method _IH_Video_QB whereIsForHome($value)
     * @method _IH_Video_QB whereIsForPost($value)
     * @method _IH_Video_QB whereCreatedAt($value)
     * @method _IH_Video_QB whereUpdatedAt($value)
     * @method Video baseSole(array|string $columns = ['*'])
     * @method Video create(array $attributes = [])
     * @method Video createOrFirst(array $attributes = [], array $values = [])
     * @method Video createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Video_C|Video[] cursor()
     * @method Video|null|_IH_Video_C|Video[] find($id, array|string $columns = ['*'])
     * @method _IH_Video_C|Video[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Video|_IH_Video_C|Video[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Video|_IH_Video_C|Video[] findOrFail($id, array|string $columns = ['*'])
     * @method Video|_IH_Video_C|Video[] findOrNew($id, array|string $columns = ['*'])
     * @method Video first(array|string $columns = ['*'])
     * @method Video firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Video firstOrCreate(array $attributes = [], array $values = [])
     * @method Video firstOrFail(array|string $columns = ['*'])
     * @method Video firstOrNew(array $attributes = [], array $values = [])
     * @method Video firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Video forceCreate(array $attributes)
     * @method Video forceCreateQuietly(array $attributes = [])
     * @method _IH_Video_C|Video[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Video_C|Video[] get(array|string $columns = ['*'])
     * @method Video getModel()
     * @method Video[] getModels(array|string $columns = ['*'])
     * @method _IH_Video_C|Video[] hydrate(array $items)
     * @method Video make(array $attributes = [])
     * @method Video newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Video[]|_IH_Video_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Video restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Video[]|_IH_Video_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Video sole(array|string $columns = ['*'])
     * @method Video updateOrCreate(array $attributes, array $values = [])
     * @method _IH_Video_QB onlyForHome()
     * @method _IH_Video_QB onlyForPost()
     * @method _IH_Video_QB published()
     */
    class _IH_Video_QB extends _BaseBuilder {}
    
    /**
     * @method Vote|null getOrPut($key, $value)
     * @method Vote|$this shift(int $count = 1)
     * @method Vote|null firstOrFail($key = null, $operator = null, $value = null)
     * @method Vote|$this pop(int $count = 1)
     * @method Vote|null pull($key, $default = null)
     * @method Vote|null last(callable $callback = null, $default = null)
     * @method Vote|$this random($number = null, bool $preserveKeys = false)
     * @method Vote|null sole($key = null, $operator = null, $value = null)
     * @method Vote|null get($key, $default = null)
     * @method Vote|null first(callable $callback = null, $default = null)
     * @method Vote|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Vote|null find($key, $default = null)
     * @method Vote[] all()
     */
    class _IH_Vote_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Vote[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method Vote baseSole(array|string $columns = ['*'])
     * @method Vote create(array $attributes = [])
     * @method Vote createOrFirst(array $attributes = [], array $values = [])
     * @method Vote createOrRestore(array $attributes = [], array $values = [])
     * @method _IH_Vote_C|Vote[] cursor()
     * @method Vote|null|_IH_Vote_C|Vote[] find($id, array|string $columns = ['*'])
     * @method _IH_Vote_C|Vote[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Vote|_IH_Vote_C|Vote[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Vote|_IH_Vote_C|Vote[] findOrFail($id, array|string $columns = ['*'])
     * @method Vote|_IH_Vote_C|Vote[] findOrNew($id, array|string $columns = ['*'])
     * @method Vote first(array|string $columns = ['*'])
     * @method Vote firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Vote firstOrCreate(array $attributes = [], array $values = [])
     * @method Vote firstOrFail(array|string $columns = ['*'])
     * @method Vote firstOrNew(array $attributes = [], array $values = [])
     * @method Vote firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Vote forceCreate(array $attributes)
     * @method Vote forceCreateQuietly(array $attributes = [])
     * @method _IH_Vote_C|Vote[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Vote_C|Vote[] get(array|string $columns = ['*'])
     * @method Vote getModel()
     * @method Vote[] getModels(array|string $columns = ['*'])
     * @method _IH_Vote_C|Vote[] hydrate(array $items)
     * @method Vote make(array $attributes = [])
     * @method Vote newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Vote[]|_IH_Vote_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Vote restoreOrCreate(array $attributes = [], array $values = [])
     * @method Paginator|Vote[]|_IH_Vote_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Vote sole(array|string $columns = ['*'])
     * @method Vote updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Vote_QB extends _BaseBuilder {}
}