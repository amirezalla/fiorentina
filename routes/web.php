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
use Botble\Base\Facades\AdminHelper;
use Botble\Blog\Http\Controllers\PostController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AdController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\NotificaController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DirettaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\WpImportController;

Route::get('send-mail',function(){
    try {
        \Illuminate\Support\Facades\Mail::send('emails.test', [], function ($message) {
            $message->to('alikeshtkar262@gmail.com')->subject("This is test e-mail");
        });
    }catch (\Exception $e){

        dd($e);
    }
});
Route::get('/migrate', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
    \Illuminate\Support\Facades\Schema::dropIfExists('likes');
    \Illuminate\Support\Facades\Schema::dropIfExists('dislikes');
    \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    \Illuminate\Support\Facades\Schema::create('likes', function (Blueprint $table) {
        $table->foreignId('comment_id')
            ->references('id')
            ->on('fob_comments')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
        $table->foreignId('member_id')
            ->references('id')
            ->on('members')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
        $table->primary(['comment_id','member_id']);
        $table->timestamps();
    });
    \Illuminate\Support\Facades\Schema::create('dislikes', function (Blueprint $table) {
        $table->foreignId('comment_id')
            ->references('id')
            ->on('fob_comments')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
        $table->foreignId('member_id')
            ->references('id')
            ->on('members')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
        $table->primary(['comment_id','member_id']);
        $table->timestamps();
    });
    dd("pl");
    $importPwDatabase = new \App\Classes\ImportPwDatabase();
//    $importPwDatabase->importUsers();
    $importPwDatabase->importPosts();
    dd("ok");
    $tables = collect(DB::connection('mysql2')->select('SHOW TABLES'))->map(fn($i) => $i->Tables_in_fiorentina)->toArray();
    $result = [];
    foreach ($tables as $table) {
        $result[$table] = DB::connection('mysql2')->table($table)->limit(5)->get()->toArray();
    }

    dd($result);
    $max = ceil(DB::connection('mysql2')->table('frntn_posts')->count() / 500);
    $number = $request->filled('number') ? $request->number : 1;
    $items = DB::connection('mysql2')
        ->table('frntn_posts')
        ->limit(100)
        ->where('post_name', 'ferrari-ho-sentito-il-presidente-siamo-tutti-uniti-col-mister-usciremo-dal-momento-difficile')
        ->get();
    dd($items, $tables);
});
Route::get('/match/{matchId}/commentaries', [MatchCommentaryController::class, 'fetchLatestCommentaries']);

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

Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
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

