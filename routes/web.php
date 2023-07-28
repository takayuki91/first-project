<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// PostControllerの追加
use App\Http\Controllers\PostController;
// 練習
// use App\Http\Controllers\TestController;

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

// 練習
// Route::get('/test', [TestController::class, 'test'])
// ->name('test');

// middlewareをかけたいルート設定
Route::middleware(['auth','admin'])->group(function () {
    // 投稿フォーム
    Route::get('post/create', [PostController::class, 'create']);

    // 投稿データ保存
    Route::post('post', [PostController::class, 'store'])
    ->name('post.store');

    // 投稿編集
    Route::get('post/{post}/edit', [PostController::class, 'edit'])
    ->name('post.edit');
    
    // 投稿更新
    Route::patch('post/{post}', [PostController::class, 'update'])
    ->name('post.update');
});

// 投稿一覧
Route::get('post', [PostController::class, 'index']);

// 投稿詳細
Route::get('post/show/{post}', [PostController::class, 'show'])
->name('post.show');

Route::get('/', function () {
    return view('welcome');
}) ->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
