<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\MatchCommentaryController;
use App\Http\Controllers\MatchStaticsController;
use App\Http\Controllers\MatchSummaryController;

use Botble\Base\Facades\AdminHelper;
use Botble\Blog\Http\Controllers\PostController;
use App\Http\Controllers\ChatSettingsController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\OutputStyle;
use App\Console\Commands\OptimizeGifs;


use App\Http\Controllers\AdController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollOneController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DirettaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WpImportController;
use App\Http\Controllers\AssetController;

use Botble\Blog\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use SimpleXMLElement;
use DOMDocument;


function addCData(SimpleXMLElement $node, string $text): void
{
    $domNode = dom_import_simplexml($node);
    $owner   = $domNode->ownerDocument ?: $domNode;
    $domNode->appendChild($owner->createCDATASection($text));
}

    Route::get('/match/{matchId}/commentaries', [MatchCommentaryController::class, 'fetchLatestCommentaries']);
    
Route::get('/match/{matchId}/sync-all-commentaries', [MatchCommentaryController::class, 'importFromApi1']);
Route::post('/match/{matchId}/refresh-summary', [MatchSummaryController::class, 'refreshMatchSummary'])
     ->name('match.refreshSummary');
     Route::get('/match/{matchId}/summary-html', [MatchSummaryController::class, 'getSummaryHtml'])
     ->name('match.summaryHtml');
     Route::post('/match/{matchId}/refresh-stats', [MatchStaticsController::class, 'refreshStats'])
     ->name('match.refreshSummary');
     Route::get('/match/{matchId}/stats-html', [MatchStaticsController::class, 'getStatsHtml'])
     ->name('match.getStatsHtml');
     Route::get('/match/{match}/score', App\Http\Controllers\ScoreController::class)
     ->name('match.score');   // → /match/123/score
     Route::get('/match/{match}/lineup-block', App\Http\Controllers\LineupController::class)
     ->name('match.lineup-block');



Route::get('/admin/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/admin/ads/create', [AdController::class, 'create'])->name('ads.create');
Route::post('/admin/ads', [AdController::class, 'store'])->name('ads.store');
Route::get('/admin/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
Route::put('/admin/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
Route::delete('/admin/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');

Route::get('/admin/ads/groups', [AdController::class, 'groupsIndex'])->name('ads.groups.index');
Route::get('/ads/sort', [AdController::class, 'sortAds'])
    ->name('ads.sort');
Route::get('/ads/sort', [AdController::class, 'sortAds'])->name('ads.sort');
Route::post('/ads/sort/update', [AdController::class, 'updateSortAds'])->name('ads.sort.update');




Route::get('/admin/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/admin/videos/create', [VideoController::class, 'create'])->name('videos.create');
Route::post('/admin/videos', [VideoController::class, 'store'])->name('videos.store');
Route::get('/admin/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
Route::put('/admin/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
Route::delete('/admin/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');

Route::get('/admin/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/admin/ads/create', [AdController::class, 'create'])->name('ads.create');
Route::post('/admin/ads', [AdController::class, 'store'])->name('ads.store');
Route::get('/admin/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
Route::put('/admin/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
Route::delete('/admin/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');
Route::get('/ads/click/{id}', [\App\Http\Controllers\AdController::class, 'trackClick'])
    ->name('ads.click');


Route::get('/admin/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/admin/players/create', [PlayerController::class, 'create'])->name('players.create');
Route::post('/admin/players', [PlayerController::class, 'store'])->name('players.store');
Route::get('/admin/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
Route::put('/admin/players/{player}', [PlayerController::class, 'update'])->name('players.update');
Route::delete('/admin/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

Route::get('/admin/votes', [VoteController::class, 'index'])->name('votes.index');
Route::get('/admin/votes/create', [VoteController::class, 'create'])->name('votes.create');
Route::post('/admin/votes', [VoteController::class, 'store'])->name('votes.store');
Route::get('/admin/votes/{vote}/edit', [VoteController::class, 'edit'])->name('votes.edit');
Route::put('/admin/votes/{vote}', [VoteController::class, 'update'])->name('votes.update');
Route::delete('/admin/votes/{vote}', [VoteController::class, 'destroy'])->name('votes.destroy');

Route::post('/polls/{matchLineup}', [PollController::class, 'store'])->name('polls.store');




Route::get('/chat/{match}', [ChatController::class, 'fetchMessages']);
Route::post('/chat/{match}', [ChatController::class, 'sendMessage']);
Route::post('/chat/{match}/status/{status}', [ChatController::class, 'updateChatStatus']);


Route::post('/notifica/store', [NotificaController::class, 'store']);


Route::get('/diretta/list', [ChatController::class, 'list'])->name('diretta.list');
Route::get('/diretta/view', [DirettaController::class, 'view'])->name('diretta.view');
Route::get('/chat-view', [DirettaController::class, 'chatView'])->name('chat.view');
Route::get('/delete-commentary', [DirettaController::class, 'ajaxDelete'])->name('delete-commentary');
Route::get('/delete-chat', [DirettaController::class, 'deleteChat'])->name('delete-chat');
Route::get('/undo-commentary', [DirettaController::class, 'ajaxRestore'])->name('undo-commentary');
Route::get('/undo-chat', [DirettaController::class, 'undoChat'])->name('undo-chat');
Route::post('/update-commentary', [DirettaController::class, 'ajaxUpdate'])->name('update-commentary');
Route::post('/store-commentary', [DirettaController::class, 'storeCommentary'])->name('store-commentary');


Route::get('/import-users-wp', [WpImportController::class, 'users']);
Route::get('/import-comment-post', [WpImportController::class, 'importComment']);
Route::get('/generate-seo', [WpImportController::class, 'generateSEO'])
     ->name('generate-seo');

Route::get('/delete-today-posts', [WpImportController::class, 'deleteTodayImportedPosts']);

Route::get('/import-posts', [WpImportController::class, 'importPostsWithoutMeta']);
Route::get('/import-meta', [WpImportController::class, 'importMetaForPosts']);
Route::get('/import-slug', [WpImportController::class, 'importSlugsForPosts']);
Route::get('/import-categories', [WpImportController::class, 'importCategories']);


Route::get('/feed', function () {

    $posts = Post::where('status', 'PUBLISHED')
        ->latest('created_at')
        ->take(30)
        ->get();

    // -------------------------------------------------
    // Build the RSS skeleton
    // -------------------------------------------------
    $xml = new SimpleXMLElement(
        '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"/>'
    );
    $xml->addAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
    $xml->addAttribute('xmlns:dc',      'http://purl.org/dc/elements/1.1/');
    $xml->addAttribute('xmlns:atom',    'http://www.w3.org/2005/Atom');
    $channel = $xml->addChild('channel');

    $channel->addChild('title',       'LaViola.it');
    $channel->addChild('link',        url('/'));
    $channel->addChild('description', 'Il sito dei tifosi viola');
    $channel->addChild('language',    'it-IT');
    $channel->addChild('lastBuildDate',
        Carbon::now('Europe/Rome')->toRfc822String());

    // -------------------------------------------------
    // Fill <item> nodes
    // -------------------------------------------------
    foreach ($posts as $post) {
        $item = $channel->addChild('item');

        $item->addChild('title',  htmlspecialchars($post->name));
        $item->addChild('link',   $post->url);
        $item->addChild('guid',   $post->url)->addAttribute('isPermaLink', 'false');
        $item->addChild('pubDate',
            Carbon::parse($post->created_at)->toRfc822String());

        // <description>
        addCData($item->addChild('description'), $post->description);

        // <content:encoded>
        $content = $item->addChild(
            'content:encoded',
            null,
            'http://purl.org/rss/1.0/modules/content/'
        );
        addCData($content, $post->content);

        // <dc:creator>
        $creator = $item->addChild(
            'dc:creator',
            null,
            'http://purl.org/dc/elements/1.1/'
        );
        addCData($creator, $post->author?->name ?? 'Redazione LaViola.it');

        // Categories (combine post categories & tags)
        foreach ($post->categories as $cat) {
            $item->addChild('category', htmlspecialchars($cat->name));
        }
        foreach ($post->tags as $tag) {
            $item->addChild('category', htmlspecialchars($tag->name));
        }
    }

    return Response::make(
        $xml->asXML(),
        200,
        ['Content-Type' => 'application/rss+xml; charset=UTF-8']
    );
});


Route::get('/optimize-gifs', function () {
    $command = new OptimizeGifs;
    $input = new ArgvInput();
    $output = new ConsoleOutput();
    $outputStyle = new OutputStyle($input, $output);
    $command->setOutput($outputStyle);
    $command->handle();
    return 'Command executed.';
});

Route::get('/asset/{path}', [AssetController::class, 'getAsset'])->where('path', '.*');
// Route::get('/ad-click/{id}', [AdController::class, 'trackClick'])->name('ad.click');


Route::get('/send-sample-email', function () {
   $recipient = 'allahverdiamirreza@gmail.com';
    // $recipient = 'alikeshtkar262@gmail.com';
    try {
        Mail::raw('This is a sample email sent from our Laravel application.', function ($message) use ($recipient) {
            $message->to($recipient)
                    ->subject('Sample Email');
        });
    } catch (\Exception $e) {
        // Log the error and dump the exception message
        Log::error('Mail error: ' . $e->getMessage());
        dd('Error sending email: ' . $e->getMessage());
    }

    return 'Sample email sent!';
});
Route::get('/test-db2-connection', function () {
    $dbHost     = env('DB_HOST2', '127.0.0.1');
    $dbPort     = env('DB_PORT2', 3306);
    $dbDatabase = env('DB_DATABASE2', 'forge');
    $dbUsername = env('DB_USERNAME2', 'forge');
    $dbPassword = env('DB_PASSWORD2', '');

    try {
        // Create a new PDO instance using environment variables
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
        $pdo = new \PDO($dsn, $dbUsername, $dbPassword);

        // Set PDO error mode to exception for better error handling
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Optional: Run a simple test query
        $stmt = $pdo->query('SELECT 1');
        $result = $stmt->fetch();

        return "DB connection successful. Test query result: " . var_export($result, true);

    } catch (\PDOException $e) {
        // Catch any connection errors
        return "DB connection failed: " . $e->getMessage();
    }
});

Route::get('/polls/create', [PollOneController::class, 'create'])->name('polls.create');
Route::post('/polls', [PollOneController::class, 'storepoll'])->name('polls.storepoll');
Route::get('/polls', [PollOneController::class, 'index'])->name('polls.index');
Route::post('/poll-options/{optionId}/vote', [PollController::class, 'vote'])->name('polls.vote');
Route::post('/pollone-options/vote', [PollOneController::class, 'vote']);
Route::get('/polls/{id}/toggle', [PollOneController::class, 'toggleActive'])->name('polls.toggle');
Route::get('/polls/{id}/export', [PollOneController::class, 'exportResults'])->name('polls.export');
Route::get('/polls/{id}/edit', [PollOneController::class, 'edit'])->name('polls.edit'); // Assumes an edit method
Route::put('polls/{id}', [PollOneController::class, 'update'])->name('polls.update');
Route::delete('/polls/{id}', [PollOneController::class, 'destroy'])->name('polls.destroy');



Route::get('posts/quick-edit-form/{id}', [PostController::class, 'quickEditForm'])
    ->name('posts.quick-edit.form');
    Route::post('posts/{id}/quick-edit', [PostController::class, 'quickEdit'])->name('posts.quick-edit');

    Route::delete('posts/{id}/soft-delete', [PostController::class, 'softDelete'])
    ->name('posts.soft-delete');

    Route::post('posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
Route::post('admin/posts/bulk-restore', [PostController::class, 'bulkRestore'])
    ->name('posts.bulk-restore');
    Route::post('admin/posts/bulk-delete', [PostController::class, 'bulkDelete'])
    ->name('posts.bulk-delete');


    Route::prefix('chat')->group(function () {
        // PATCH = update
        Route::patch('/{message}', [ChatController::class, 'update'])
             ->name('chat.update');
    
        // DELETE = soft delete
        Route::delete('/{message}', [ChatController::class, 'destroy'])
             ->name('chat.destroy');

             Route::get('/body/{match}', [ChatController::class, 'body'])
     ->name('chat.body');

     /* trash list (deleted) */
Route::get ('/trash-body/{match}', [ChatController::class, 'trashBody'])
->name('chat.trash.body');

/* bulk actions */
Route::delete('/bulk',         [ChatController::class, 'bulkDelete'])
->name('chat.bulkDelete');

Route::post  ('/bulk-restore', [ChatController::class, 'bulkRestore'])
->name('chat.bulkRestore');
    });




    Route::get('/chat-settings', [ChatSettingsController::class, 'index'])->name('chat-settings.index');
    Route::post('/chat-settings/update-light-words', [ChatSettingsController::class, 'updateLightWords'])->name('chat-settings.update-light-words');
    Route::post('/chat-settings/update-auto-message', [ChatSettingsController::class, 'updateAutoMessage'])->name('chat-settings.update-auto-message');
    