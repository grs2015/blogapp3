<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagContoller;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\BaseinfoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Trash\UserTrashController;
use App\Http\Controllers\Admin\PostCommentController;

use App\Http\Controllers\Member\PostRatingController;



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


// Route::get('/', function() {
//     return Post::search('Similique')->get();
// });

/* ------------------------------- Public part ------------------------------ */

Route::name('posts.')->group(function() {
    Route::get('/posts', [App\Http\Controllers\Public\PostController::class, 'index'])->name('index');
    Route::get('/posts/{post:slug}', [App\Http\Controllers\Public\PostController::class, 'show'])->name('show');
});

/* ------------------------------- Admin part ------------------------------- */
// Dashboard
// Here goes the root with Auth middleware

// Admin part
Route::middleware('auth')->group(function() {
    Route::prefix('admin')->group(function() {
        Route::middleware('role:super-admin|admin')->group(function() {
            Route::name('admin.')->group(function() {
                // Admin routes
                Route::resource('tags', App\Http\Controllers\Admin\TagController::class);
                Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
                Route::resource('posts.postmetas', App\Http\Controllers\Admin\PostPostmetaController::class);
                Route::resource('baseinfo', App\Http\Controllers\Admin\BaseinfoController::class);
                Route::resource('posts.comments', App\Http\Controllers\Admin\PostCommentController::class);

                Route::resource('users', App\Http\Controllers\Admin\UserController::class);
                Route::name('users.')->group(function() {
                    Route::post('/users/delete', [App\Http\Controllers\Admin\UserController::class, 'delete'])->name('forcedelete');
                    Route::post('/users/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('restore');
                });

                Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
                Route::name('users.')->group(function() {
                    Route::post('/posts/delete', [App\Http\Controllers\Admin\PostController::class, 'delete'])->name('forcedelete');
                    Route::post('/posts/restore', [App\Http\Controllers\Admin\PostController::class, 'restore'])->name('restore');
                });
            });
        });
    });

    Route::prefix('author')->group(function() {
        Route::middleware('role:author|admin|super-admin')->group(function() {
            Route::name('author.')->group(function() {
                // Users routes
                Route::resource('tags', App\Http\Controllers\Author\TagController::class)->only(['index', 'show']);
                Route::resource('categories', App\Http\Controllers\Author\CategoryController::class)->only(['index', 'show']);
                Route::resource('posts.postmetas', App\Http\Controllers\Author\PostPostmetaController::class);
                Route::resource('posts', App\Http\Controllers\Author\PostController::class)->except(['destroy']);
            });
        });
    });

    Route::prefix('member')->group(function() {
        Route::middleware('role:member|admin|super-admin')->group(function() {
            Route::name('member')->group(function() {
                // Member routes
                Route::resource('posts.comments', App\Http\Controllers\Member\PostCommentController::class)->only(['create', 'store']);
                Route::post('posts/{post:slug}/rate', [PostRatingController::class, 'store'])->name('rate');
                // Route::post('posts/{post:slug}/comment', [PostCommentController::class, 'store'])->name('comment');
            });
        });
    });

});



// // Specific user posts end-points
// Route::name('users.posts.')->group(function() {
//     Route::get('/users/{user}/posts', [UserPostController::class, 'index'])->name('index');
//     Route::get('/users/{user}/posts/create', [UserPostController::class, 'create'])->name('create');
//     Route::get('/users/{user}/posts/{post:slug}', [UserPostController::class, 'show'])->name('show');
//     Route::get('/users/{user}/posts/{post:slug}/edit', [UserPostController::class, 'edit'])->name('edit');
//     Route::post('/users/{user}/posts', [UserPostController::class, 'store'])->name('store');
//     Route::put('/users/{user}/posts/{post:slug}', [UserPostController::class, 'update'])->name('update');
//     Route::delete('/users/{user}/posts/{post:slug}', [UserPostController::class, 'destroy'])->name('delete');

//     Route::post('/users/{user}/posts/delete', [UserPostTrashController::class, 'destroy'])->name('forcedelete');
//     Route::post('/users/{user}/posts/restore', [UserPostTrashController::class, 'restore'])->name('restore');
// });

// Route::name('tags.')->group(function() {
//     Route::get('/tags', [TagController::class, 'index'])->name('index');
//     Route::get('/tags/create', [TagController::class, 'create'])->name('create');
//     Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('show');
//     Route::get('/tags/{tag:slug}/edit', [TagController::class, 'edit'])->name('edit');
//     Route::post('/tags', [TagController::class, 'store'])->name('store');
//     Route::put('/tags/{tag:slug}', [TagController::class, 'update'])->name('update');
//     Route::delete('/tags/{tag:slug}', [TagController::class, 'destroy'])->name('delete');
// });

// Route::name('categories.')->group(function() {
//     Route::get('/categories', [CategoryController::class, 'index'])->name('index');
//     Route::get('/categories/create', [CategoryController::class, 'create'])->name('create');
//     Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('show');
//     Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('edit');
//     Route::post('/categories', [CategoryController::class, 'store'])->name('store');
//     Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('update');
//     Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('delete');
// });

// Route::get('/comments/create', [PostCommentController::class, 'create'])->name('comments.create');
// Route::resource('posts.comments', PostCommentController::class);

// Route::get('/postmetas/create', [PostPostmetaController::class, 'create'])->name('postmetas.create');
// Route::resource('posts.postmetas', PostPostmetaController::class);

// Route::get('/baseinfo/create', [BaseinfoController::class, 'create'])->name('baseinfos.create');
// Route::resource('/baseinfo', BaseinfoController::class);

// Other routes are assigned thru Fortify package
// Route::name('users.')->group(function() {
//     Route::get('/users', [UserController::class, 'index'])->name('index');
//     Route::get('/users/{user}', [UserController::class, 'show'])->name('show');
//     Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('destroy');

//     Route::post('/users/delete', [UserTrashController::class, 'destroy'])->name('forcedelete');
//     Route::post('/users/restore', [UserTrashController::class, 'restore'])->name('restore');
// });
