<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BaseinfoController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\PostRatingController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostPostmetaController;
use App\Http\Controllers\Trash\UserPostTrashController;

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

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::post('posts/{post:slug}/rate', [PostRatingController::class, 'store'])->name('posts.rate')->middleware('auth');



/* ------------------------------- Admin part ------------------------------- */
// Dashboard

// Specific user posts end-points
Route::name('users.posts.')->group(function() {
    Route::get('/users/{user}/posts', [UserPostController::class, 'index'])->name('index');
    Route::get('/users/{user}/posts/create', [UserPostController::class, 'create'])->name('create');
    Route::get('/users/{user}/posts/{post:slug}', [UserPostController::class, 'show'])->name('show');
    Route::get('/users/{user}/posts/{post:slug}/edit', [UserPostController::class, 'edit'])->name('edit');
    Route::post('/users/{user}/posts', [UserPostController::class, 'store'])->name('store');
    Route::put('/users/{user}/posts/{post:slug}', [UserPostController::class, 'update'])->name('update');
    Route::delete('/users/{user}/posts/{post:slug}', [UserPostController::class, 'destroy'])->name('delete');

    Route::post('/users/{user}/posts/delete', [UserPostTrashController::class, 'destroy'])->name('forcedelete');
    Route::post('/users/{user}/posts/restore', [UserPostTrashController::class, 'restore'])->name('restore');
});

Route::name('tags.')->group(function() {
    Route::get('/tags', [TagController::class, 'index'])->name('index');
    Route::get('/tags/create', [TagController::class, 'create'])->name('create');
    Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('show');
    Route::get('/tags/{tag:slug}/edit', [TagController::class, 'edit'])->name('edit');
    Route::post('/tags', [TagController::class, 'store'])->name('store');
    Route::put('/tags/{tag:slug}', [TagController::class, 'update'])->name('update');
    Route::delete('/tags/{tag:slug}', [TagController::class, 'destroy'])->name('delete');
});

Route::name('categories.')->group(function() {
    Route::get('/categories', [CategoryController::class, 'index'])->name('index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('create');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('show');
    Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::post('/categories', [CategoryController::class, 'store'])->name('store');
    Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('delete');
});

Route::get('/comments/create', [PostCommentController::class, 'create'])->name('comments.create');
Route::resource('posts.comments', PostCommentController::class);

Route::get('/postmetas/create', [PostPostmetaController::class, 'create'])->name('postmetas.create');
Route::resource('posts.postmetas', PostPostmetaController::class);

Route::get('/baseinfo/create', [BaseinfoController::class, 'create'])->name('baseinfos.create');
Route::resource('/baseinfo', BaseinfoController::class);

// Other routes are assigned thru Fortify package
Route::name('users.')->group(function() {
    Route::get('/users', [UserController::class, 'index'])->name('index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('show');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('destroy');
});
