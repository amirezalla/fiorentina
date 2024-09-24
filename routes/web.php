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
use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\NotificaController;



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





Route::post('/notifica/store', [NotificaController::class, 'store']);