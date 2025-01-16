<?php //4e3b4a4ea42324bbdd26653b8dd67891
/** @noinspection all */

namespace App\Models {

    use Botble\Base\Models\MetaBox;
    use Botble\Media\Models\MediaFile;
    use Botble\Member\Models\Member;
    use Database\Factories\UserFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Support\Carbon;
    use Laravel\Sanctum\PersonalAccessToken;
    use LaravelIdea\Helper\App\Models\_IH_AdPosition_C;
    use LaravelIdea\Helper\App\Models\_IH_AdPosition_QB;
    use LaravelIdea\Helper\App\Models\_IH_AdType_C;
    use LaravelIdea\Helper\App\Models\_IH_AdType_QB;
    use LaravelIdea\Helper\App\Models\_IH_Ad_C;
    use LaravelIdea\Helper\App\Models\_IH_Ad_QB;
    use LaravelIdea\Helper\App\Models\_IH_Calendario_C;
    use LaravelIdea\Helper\App\Models\_IH_Calendario_QB;
    use LaravelIdea\Helper\App\Models\_IH_LiveChat_C;
    use LaravelIdea\Helper\App\Models\_IH_LiveChat_QB;
    use LaravelIdea\Helper\App\Models\_IH_MatchCommentary_C;
    use LaravelIdea\Helper\App\Models\_IH_MatchCommentary_QB;
    use LaravelIdea\Helper\App\Models\_IH_Matches_C;
    use LaravelIdea\Helper\App\Models\_IH_Matches_QB;
    use LaravelIdea\Helper\App\Models\_IH_MatchLineups_C;
    use LaravelIdea\Helper\App\Models\_IH_MatchLineups_QB;
    use LaravelIdea\Helper\App\Models\_IH_MatchStatics_C;
    use LaravelIdea\Helper\App\Models\_IH_MatchStatics_QB;
    use LaravelIdea\Helper\App\Models\_IH_MatchSummary_C;
    use LaravelIdea\Helper\App\Models\_IH_MatchSummary_QB;
    use LaravelIdea\Helper\App\Models\_IH_Message_C;
    use LaravelIdea\Helper\App\Models\_IH_Message_QB;
    use LaravelIdea\Helper\App\Models\_IH_Notifica_C;
    use LaravelIdea\Helper\App\Models\_IH_Notifica_QB;
    use LaravelIdea\Helper\App\Models\_IH_PlayerStats_C;
    use LaravelIdea\Helper\App\Models\_IH_PlayerStats_QB;
    use LaravelIdea\Helper\App\Models\_IH_PlayerVotes_C;
    use LaravelIdea\Helper\App\Models\_IH_PlayerVotes_QB;
    use LaravelIdea\Helper\App\Models\_IH_Player_C;
    use LaravelIdea\Helper\App\Models\_IH_Player_QB;
    use LaravelIdea\Helper\App\Models\_IH_PollOption_C;
    use LaravelIdea\Helper\App\Models\_IH_PollOption_QB;
    use LaravelIdea\Helper\App\Models\_IH_Poll_C;
    use LaravelIdea\Helper\App\Models\_IH_Poll_QB;
    use LaravelIdea\Helper\App\Models\_IH_Standing_C;
    use LaravelIdea\Helper\App\Models\_IH_Standing_QB;
    use LaravelIdea\Helper\App\Models\_IH_User_C;
    use LaravelIdea\Helper\App\Models\_IH_User_QB;
    use LaravelIdea\Helper\App\Models\_IH_VideoAd_C;
    use LaravelIdea\Helper\App\Models\_IH_VideoAd_QB;
    use LaravelIdea\Helper\App\Models\_IH_VideoSpec_C;
    use LaravelIdea\Helper\App\Models\_IH_VideoSpec_QB;
    use LaravelIdea\Helper\App\Models\_IH_Video_C;
    use LaravelIdea\Helper\App\Models\_IH_Video_QB;
    use LaravelIdea\Helper\App\Models\_IH_Vote_C;
    use LaravelIdea\Helper\App\Models\_IH_Vote_QB;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_C;
    use LaravelIdea\Helper\Botble\Base\Models\_IH_MetaBox_QB;
    use LaravelIdea\Helper\Botble\Media\Models\_IH_MediaFile_C;
    use LaravelIdea\Helper\Botble\Media\Models\_IH_MediaFile_QB;
    use LaravelIdea\Helper\Botble\Member\Models\_IH_Member_QB;
    use LaravelIdea\Helper\Illuminate\Notifications\_IH_DatabaseNotification_QB;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_C;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_QB;
    
    /**
     * @property-read string $group_name attribute
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_Ad_QB onWriteConnection()
     * @method _IH_Ad_QB newQuery()
     * @method static _IH_Ad_QB on(null|string $connection = null)
     * @method static _IH_Ad_QB query()
     * @method static _IH_Ad_QB with(array|string $relations)
     * @method _IH_Ad_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Ad_C|Ad[] all()
     * @mixin _IH_Ad_QB
     */
    class Ad extends Model {}
    
    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_AdPosition_QB onWriteConnection()
     * @method _IH_AdPosition_QB newQuery()
     * @method static _IH_AdPosition_QB on(null|string $connection = null)
     * @method static _IH_AdPosition_QB query()
     * @method static _IH_AdPosition_QB with(array|string $relations)
     * @method _IH_AdPosition_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AdPosition_C|AdPosition[] all()
     * @mixin _IH_AdPosition_QB
     */
    class AdPosition extends Model {}
    
    /**
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @method static _IH_AdType_QB onWriteConnection()
     * @method _IH_AdType_QB newQuery()
     * @method static _IH_AdType_QB on(null|string $connection = null)
     * @method static _IH_AdType_QB query()
     * @method static _IH_AdType_QB with(array|string $relations)
     * @method _IH_AdType_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AdType_C|AdType[] all()
     * @mixin _IH_AdType_QB
     */
    class AdType extends Model {}
    
    /**
     * @property _IH_Vote_C|Vote[] $votes
     * @property-read int $votes_count
     * @method HasMany|_IH_Vote_QB votes()
     * @method static _IH_Calendario_QB onWriteConnection()
     * @method _IH_Calendario_QB newQuery()
     * @method static _IH_Calendario_QB on(null|string $connection = null)
     * @method static _IH_Calendario_QB query()
     * @method static _IH_Calendario_QB with(array|string $relations)
     * @method _IH_Calendario_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Calendario_C|Calendario[] all()
     * @mixin _IH_Calendario_QB
     */
    class Calendario extends Model {}
    
    /**
     * @property Calendario $match
     * @method BelongsTo|_IH_Calendario_QB match()
     * @method static _IH_LiveChat_QB onWriteConnection()
     * @method _IH_LiveChat_QB newQuery()
     * @method static _IH_LiveChat_QB on(null|string $connection = null)
     * @method static _IH_LiveChat_QB query()
     * @method static _IH_LiveChat_QB with(array|string $relations)
     * @method _IH_LiveChat_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_LiveChat_C|LiveChat[] all()
     * @mixin _IH_LiveChat_QB
     */
    class LiveChat extends Model {}
    
    /**
     * @method static _IH_MatchCommentary_QB onWriteConnection()
     * @method _IH_MatchCommentary_QB newQuery()
     * @method static _IH_MatchCommentary_QB on(null|string $connection = null)
     * @method static _IH_MatchCommentary_QB query()
     * @method static _IH_MatchCommentary_QB with(array|string $relations)
     * @method _IH_MatchCommentary_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MatchCommentary_C|MatchCommentary[] all()
     * @mixin _IH_MatchCommentary_QB
     */
    class MatchCommentary extends Model {}
    
    /**
     * @property Matches $match
     * @method BelongsTo|_IH_Matches_QB match()
     * @method static _IH_MatchLineups_QB onWriteConnection()
     * @method _IH_MatchLineups_QB newQuery()
     * @method static _IH_MatchLineups_QB on(null|string $connection = null)
     * @method static _IH_MatchLineups_QB query()
     * @method static _IH_MatchLineups_QB with(array|string $relations)
     * @method _IH_MatchLineups_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MatchLineups_C|MatchLineups[] all()
     * @mixin _IH_MatchLineups_QB
     */
    class MatchLineups extends Model {}
    
    /**
     * @method static _IH_MatchStatics_QB onWriteConnection()
     * @method _IH_MatchStatics_QB newQuery()
     * @method static _IH_MatchStatics_QB on(null|string $connection = null)
     * @method static _IH_MatchStatics_QB query()
     * @method static _IH_MatchStatics_QB with(array|string $relations)
     * @method _IH_MatchStatics_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MatchStatics_C|MatchStatics[] all()
     * @mixin _IH_MatchStatics_QB
     */
    class MatchStatics extends Model {}
    
    /**
     * @method static _IH_MatchSummary_QB onWriteConnection()
     * @method _IH_MatchSummary_QB newQuery()
     * @method static _IH_MatchSummary_QB on(null|string $connection = null)
     * @method static _IH_MatchSummary_QB query()
     * @method static _IH_MatchSummary_QB with(array|string $relations)
     * @method _IH_MatchSummary_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_MatchSummary_C|MatchSummary[] all()
     * @mixin _IH_MatchSummary_QB
     */
    class MatchSummary extends Model {}
    
    /**
     * @property int $id
     * @property int $match_id
     * @property string|null $venue
     * @property int $matchday
     * @property string $stage
     * @property string|null $group
     * @property Carbon $match_date
     * @property string $status
     * @property array|null $home_team
     * @property array|null $away_team
     * @property array|null $score
     * @property array|null $goals
     * @property array|null $penalties
     * @property array|null $bookings
     * @property array|null $substitutions
     * @property array|null $odds
     * @property array|null $referees
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_Player_C|Player[] $players
     * @property-read int $players_count
     * @method HasMany|_IH_Player_QB players()
     * @method static _IH_Matches_QB onWriteConnection()
     * @method _IH_Matches_QB newQuery()
     * @method static _IH_Matches_QB on(null|string $connection = null)
     * @method static _IH_Matches_QB query()
     * @method static _IH_Matches_QB with(array|string $relations)
     * @method _IH_Matches_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Matches_C|Matches[] all()
     * @mixin _IH_Matches_QB
     */
    class Matches extends Model {}
    
    /**
     * @property Calendario $match
     * @method BelongsTo|_IH_Calendario_QB match()
     * @property Member $member
     * @method BelongsTo|_IH_Member_QB member()
     * @method static _IH_Message_QB onWriteConnection()
     * @method _IH_Message_QB newQuery()
     * @method static _IH_Message_QB on(null|string $connection = null)
     * @method static _IH_Message_QB query()
     * @method static _IH_Message_QB with(array|string $relations)
     * @method _IH_Message_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Message_C|Message[] all()
     * @mixin _IH_Message_QB
     */
    class Message extends Model {}
    
    /**
     * @method static _IH_Notifica_QB onWriteConnection()
     * @method _IH_Notifica_QB newQuery()
     * @method static _IH_Notifica_QB on(null|string $connection = null)
     * @method static _IH_Notifica_QB query()
     * @method static _IH_Notifica_QB with(array|string $relations)
     * @method _IH_Notifica_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Notifica_C|Notifica[] all()
     * @mixin _IH_Notifica_QB
     */
    class Notifica extends Model {}
    
    /**
     * @property _IH_Vote_C|Vote[] $votes
     * @property-read int $votes_count
     * @method HasMany|_IH_Vote_QB votes()
     * @method static _IH_Player_QB onWriteConnection()
     * @method _IH_Player_QB newQuery()
     * @method static _IH_Player_QB on(null|string $connection = null)
     * @method static _IH_Player_QB query()
     * @method static _IH_Player_QB with(array|string $relations)
     * @method _IH_Player_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Player_C|Player[] all()
     * @mixin _IH_Player_QB
     */
    class Player extends Model {}
    
    /**
     * @method static _IH_PlayerStats_QB onWriteConnection()
     * @method _IH_PlayerStats_QB newQuery()
     * @method static _IH_PlayerStats_QB on(null|string $connection = null)
     * @method static _IH_PlayerStats_QB query()
     * @method static _IH_PlayerStats_QB with(array|string $relations)
     * @method _IH_PlayerStats_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PlayerStats_C|PlayerStats[] all()
     * @mixin _IH_PlayerStats_QB
     */
    class PlayerStats extends Model {}
    
    /**
     * @property Matches $match
     * @method BelongsTo|_IH_Matches_QB match()
     * @property Player $player
     * @method BelongsTo|_IH_Player_QB player()
     * @method static _IH_PlayerVotes_QB onWriteConnection()
     * @method _IH_PlayerVotes_QB newQuery()
     * @method static _IH_PlayerVotes_QB on(null|string $connection = null)
     * @method static _IH_PlayerVotes_QB query()
     * @method static _IH_PlayerVotes_QB with(array|string $relations)
     * @method _IH_PlayerVotes_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PlayerVotes_C|PlayerVotes[] all()
     * @mixin _IH_PlayerVotes_QB
     */
    class PlayerVotes extends Model {}
    
    /**
     * @property _IH_PollOption_C|PollOption[] $options
     * @property-read int $options_count
     * @method HasMany|_IH_PollOption_QB options()
     * @method static _IH_Poll_QB onWriteConnection()
     * @method _IH_Poll_QB newQuery()
     * @method static _IH_Poll_QB on(null|string $connection = null)
     * @method static _IH_Poll_QB query()
     * @method static _IH_Poll_QB with(array|string $relations)
     * @method _IH_Poll_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Poll_C|Poll[] all()
     * @mixin _IH_Poll_QB
     */
    class Poll extends Model {}
    
    /**
     * @property Poll $poll
     * @method BelongsTo|_IH_Poll_QB poll()
     * @method static _IH_PollOption_QB onWriteConnection()
     * @method _IH_PollOption_QB newQuery()
     * @method static _IH_PollOption_QB on(null|string $connection = null)
     * @method static _IH_PollOption_QB query()
     * @method static _IH_PollOption_QB with(array|string $relations)
     * @method _IH_PollOption_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PollOption_C|PollOption[] all()
     * @mixin _IH_PollOption_QB
     */
    class PollOption extends Model {}
    
    /**
     * @property int $id
     * @property int $position
     * @property int $team_id
     * @property string $team_name
     * @property string $short_name
     * @property string $tla
     * @property string $crest_url
     * @property int $played_games
     * @property string|null $form
     * @property int $won
     * @property int $draw
     * @property int $lost
     * @property int $points
     * @property int $goals_for
     * @property int $goals_against
     * @property int $goal_difference
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_Standing_QB onWriteConnection()
     * @method _IH_Standing_QB newQuery()
     * @method static _IH_Standing_QB on(null|string $connection = null)
     * @method static _IH_Standing_QB query()
     * @method static _IH_Standing_QB with(array|string $relations)
     * @method _IH_Standing_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Standing_C|Standing[] all()
     * @mixin _IH_Standing_QB
     */
    class Standing extends Model {}
    
    /**
     * @property int $id
     * @property string $email
     * @property Carbon|null $email_verified_at
     * @property mixed|null $password
     * @property string|null $remember_token
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string|null $first_name
     * @property string|null $last_name
     * @property string|null $username
     * @property int|null $avatar_id
     * @property bool $super_user
     * @property bool $manage_supers
     * @property string|null $permissions
     * @property Carbon|null $last_login
     * @property DatabaseNotificationCollection|DatabaseNotification[] $notifications
     * @property-read int $notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB notifications()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $readNotifications
     * @property-read int $read_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB readNotifications()
     * @property _IH_PersonalAccessToken_C|PersonalAccessToken[] $tokens
     * @property-read int $tokens_count
     * @method MorphToMany|_IH_PersonalAccessToken_QB tokens()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $unreadNotifications
     * @property-read int $unread_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB unreadNotifications()
     * @method static _IH_User_QB onWriteConnection()
     * @method _IH_User_QB newQuery()
     * @method static _IH_User_QB on(null|string $connection = null)
     * @method static _IH_User_QB query()
     * @method static _IH_User_QB with(array|string $relations)
     * @method _IH_User_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_User_C|User[] all()
     * @foreignLinks id,\Botble\Revision\Revision,user_id|id,\Botble\AuditLog\Models\AuditHistory,user_id|id,\Botble\RequestLog\Models\RequestLog,user_id|id,\Botble\ACL\Models\Activation,user_id|id,\Botble\ACL\Models\UserMeta,user_id|id,\Botble\Page\Models\Page,user_id|id,\Botble\Gallery\Models\Gallery,user_id|id,\Botble\Dashboard\Models\DashboardWidgetSetting,user_id|id,\Botble\Block\Models\Block,user_id|id,\Botble\Media\Models\MediaFolder,user_id|id,\Botble\Media\Models\MediaFile,user_id|id,\Botble\Media\Models\MediaSetting,user_id
     * @mixin _IH_User_QB
     * @method static UserFactory factory(array|callable|int|null $count = null, array|callable $state = [])
     */
    class User extends Model {}
    
    /**
     * @property int $id
     * @property string $title
     * @property bool $is_random
     * @property Carbon|null $published_at
     * @property bool $is_for_home
     * @property bool $is_for_post
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_MediaFile_C|MediaFile[] $mediaFiles
     * @property-read int $media_files_count
     * @method BelongsToMany|_IH_MediaFile_QB mediaFiles()
     * @method static _IH_Video_QB onWriteConnection()
     * @method _IH_Video_QB newQuery()
     * @method static _IH_Video_QB on(null|string $connection = null)
     * @method static _IH_Video_QB query()
     * @method static _IH_Video_QB with(array|string $relations)
     * @method _IH_Video_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Video_C|Video[] all()
     * @foreignLinks 
     * @mixin _IH_Video_QB
     */
    class Video extends Model {}
    
    /**
     * @property Video $video
     * @method BelongsTo|_IH_Video_QB video()
     * @method static _IH_VideoAd_QB onWriteConnection()
     * @method _IH_VideoAd_QB newQuery()
     * @method static _IH_VideoAd_QB on(null|string $connection = null)
     * @method static _IH_VideoAd_QB query()
     * @method static _IH_VideoAd_QB with(array|string $relations)
     * @method _IH_VideoAd_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_VideoAd_C|VideoAd[] all()
     * @mixin _IH_VideoAd_QB
     */
    class VideoAd extends Model {}
    
    /**
     * @property Video $video
     * @method BelongsTo|_IH_Video_QB video()
     * @method static _IH_VideoSpec_QB onWriteConnection()
     * @method _IH_VideoSpec_QB newQuery()
     * @method static _IH_VideoSpec_QB on(null|string $connection = null)
     * @method static _IH_VideoSpec_QB query()
     * @method static _IH_VideoSpec_QB with(array|string $relations)
     * @method _IH_VideoSpec_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_VideoSpec_C|VideoSpec[] all()
     * @mixin _IH_VideoSpec_QB
     */
    class VideoSpec extends Model {}
    
    /**
     * @property Calendario $calendario
     * @method BelongsTo|_IH_Calendario_QB calendario()
     * @property Matches $match
     * @method BelongsTo|_IH_Matches_QB match()
     * @property _IH_MetaBox_C|MetaBox[] $metadata
     * @property-read int $metadata_count
     * @method MorphToMany|_IH_MetaBox_QB metadata()
     * @property Player $player
     * @method BelongsTo|_IH_Player_QB player()
     * @method static _IH_Vote_QB onWriteConnection()
     * @method _IH_Vote_QB newQuery()
     * @method static _IH_Vote_QB on(null|string $connection = null)
     * @method static _IH_Vote_QB query()
     * @method static _IH_Vote_QB with(array|string $relations)
     * @method _IH_Vote_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Vote_C|Vote[] all()
     * @mixin _IH_Vote_QB
     */
    class Vote extends Model {}
}