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
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DirettaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WpImportController;
use App\Http\Controllers\AssetController;



Route::get('/match/{matchId}/commentaries', [MatchCommentaryController::class, 'fetchLatestCommentaries']);
Route::get('/match/{matchId}/sync-all-commentaries', [MatchCommentaryController::class, 'storeCommentariesAndRegenerateJson']);
Route::post('/match/{matchId}/refresh-summary', [MatchSummaryController::class, 'refreshMatchSummary'])
     ->name('match.refreshSummary');
     Route::get('/match/{matchId}/summary-html', [MatchSummaryController::class, 'getSummaryHtml'])
     ->name('match.summaryHtml');
     Route::post('/match/{matchId}/refresh-stats', [MatchStaticsController::class, 'refreshStats'])
     ->name('match.refreshSummary');
     Route::get('/match/{matchId}/stats-html', [MatchStaticsController::class, 'getStatsHtml'])
     ->name('match.getStatsHtml');


Route::get('/admin/ads', [AdController::class, 'index'])->name('ads.index');
Route::get('/admin/ads/create', [AdController::class, 'create'])->name('ads.create');
Route::post('/admin/ads', [AdController::class, 'store'])->name('ads.store');
Route::get('/admin/ads/{ad}/edit', [AdController::class, 'edit'])->name('ads.edit');
Route::put('/admin/ads/{ad}', [AdController::class, 'update'])->name('ads.update');
Route::delete('/admin/ads/{ad}', [AdController::class, 'destroy'])->name('ads.destroy');


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

Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
Route::post('/polls', [PollController::class, 'storepoll'])->name('polls.storepoll');
Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
Route::post('/poll-options/{optionId}/vote', [PollController::class, 'vote'])->name('polls.vote');
Route::get('/polls/{id}/toggle', [PollController::class, 'toggleActive'])->name('polls.toggle');
Route::get('/polls/{id}/export', [PollController::class, 'exportResults'])->name('polls.export');
Route::get('/polls/{id}/edit', [PollController::class, 'edit'])->name('polls.edit'); // Assumes an edit method
Route::delete('/polls/{id}', [PollController::class, 'destroy'])->name('polls.destroy');


Route::get('/chat/{match}', [ChatController::class, 'fetchMessages']);
Route::post('/chat/{match}', [ChatController::class, 'sendMessage']);
Route::post('/chat/{match}/status/{status}', [ChatController::class, 'updateChatStatus']);


Route::post('/notifica/store', [NotificaController::class, 'store']);


Route::get('/diretta/list', [ChatController::class, 'list'])->name('diretta.list');
Route::get('/diretta/view', [DirettaController::class, 'view'])->name('diretta.view');
Route::get('/chat-view', [DirettaController::class, 'chatView'])->name('chat.view');
Route::get('/delete-commentary', [DirettaController::class, 'deleteCommentary'])->name('delete-commentary');
Route::get('/delete-chat', [DirettaController::class, 'deleteChat'])->name('delete-chat');
Route::get('/undo-commentary', [DirettaController::class, 'undoCommentary'])->name('undo-commentary');
Route::get('/undo-chat', [DirettaController::class, 'undoChat'])->name('undo-chat');
Route::post('/update-commentary', [DirettaController::class, 'updateCommentary'])->name('update-commentary');
Route::post('/store-commentary', [DirettaController::class, 'storeCommentary'])->name('store-commentary');


Route::get('/import-users-wp', [WpImportController::class, 'users']);
Route::get('/import-comment-post', [WpImportController::class, 'importComment']);
Route::get('/generate-seo', [WpImportController::class, 'generateSEO']);

Route::get('/delete-today-posts', [WpImportController::class, 'deleteTodayImportedPosts']);

Route::get('/import-posts', [WpImportController::class, 'importPostsWithoutMeta']);
Route::get('/import-meta', [WpImportController::class, 'importMetaForPosts']);
Route::get('/import-slug', [WpImportController::class, 'importSlugsForPosts']);
Route::get('/import-categories', [WpImportController::class, 'importCategories']);



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





Route::get('posts/quick-edit-form/{id}', [PostController::class, 'quickEditForm'])
    ->name('posts.quick-edit.form');
    Route::post('posts/{id}/quick-edit', [PostController::class, 'quickEdit'])->name('posts.quick-edit');

