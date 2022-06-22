<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

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

Route::name('users.posts.')->group(function() {
    Route::get('/users/{user}/posts', [UserPostController::class, 'index'])->name('index');
    Route::get('/users/{user}/posts/{post:slug}', [UserPostController::class, 'show'])->name('show');
    Route::get('/users/{user}/posts/create', [UserPostController::class, 'create'])->name('create');
    Route::get('/users/{user}/posts/{post:slug}/edit', [UserPostController::class, 'edit'])->name('edit');
    Route::post('/users/{user}/posts', [UserPostController::class, 'store'])->name('store');
    Route::put('/users/{user}/posts/{post:slug}', [UserPostController::class, 'update'])->name('update');
    Route::delete('/users/{user}/posts/{post:slug}', [UserPostController::class, 'destroy'])->name('delete');
});
