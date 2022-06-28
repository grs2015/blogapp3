<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserPostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* ------------------------------- Public part ------------------------------ */

// TODO - public routes for index/show methods

/* ------------------------------- Admin part ------------------------------- */
// Dashboard

Route::get('/posts/create', [UserPostController::class, 'create'])->name('posts.create');
// Specific user posts end-points
Route::name('users.posts.')->group(function() {
    Route::get('/users/{user}/posts', [UserPostController::class, 'index'])->name('index');
    Route::get('/users/{user}/posts/{post:slug}', [UserPostController::class, 'show'])->name('show');
    Route::get('/users/{user}/posts/{post:slug}/edit', [UserPostController::class, 'edit'])->name('edit');
    Route::post('/users/{user}/posts', [UserPostController::class, 'store'])->name('store');
    Route::put('/users/{user}/posts/{post:slug}', [UserPostController::class, 'update'])->name('update');
    Route::delete('/users/{user}/posts/{post:slug}', [UserPostController::class, 'destroy'])->name('delete');
});

Route::name('tags.')->group(function() {
    Route::get('/tags', [TagController::class, 'index'])->name('index');
    Route::get('/tags/create', [TagController::class, 'create'])->name('create');
    Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('show');
    Route::get('/tags/{tag:slug}/edit', [TagController::class, 'edit'])->name('edit');
    Route::post('/tags', [TagController::class, 'store'])->name('store');
    Route::post('/tags/{tag:slug}', [TagController::class, 'update'])->name('update');
    Route::post('/tags/{tag:slug}', [TagController::class, 'destroy'])->name('delete');
});
