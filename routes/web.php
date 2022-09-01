<?php

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\TagContoller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\BaseinfoController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryMassDeleteController;
use App\Http\Controllers\Trash\UserTrashController;

use App\Http\Controllers\Admin\PostCommentController;
use App\Http\Controllers\Member\PostRatingController;
use App\Http\Middleware\EnsureUserIsEnabled;

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
    return Inertia::render('Public/Index', [
        'name' => 'Grigorii'
    ]);
});

// Route::get('/', function() {
//     return Post::search('Similique')->get();
// });

/* ------------------------------- Public part ------------------------------ */

Route::name('posts.')->group(function() {
    Route::get('/posts', [App\Http\Controllers\Public\PostController::class, 'index'])->name('index');
    Route::get('/posts/{post:slug}', [App\Http\Controllers\Public\PostController::class, 'show'])->name('show');
});

Route::name('public.')->group(function() {
    Route::resource('baseinfo', App\Http\Controllers\Public\BaseinfoController::class)->only(['index']);
    Route::resource('categories', App\Http\Controllers\Public\CategoryController::class)->only(['index', 'show']);
    Route::resource('tags', App\Http\Controllers\Public\TagController::class)->only(['index', 'show']);
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
                Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('index');

                Route::middleware(EnsureUserIsEnabled::class)->group(function() {
                    Route::name('avatar.')->group(function() {
                        Route::put('/avatar', [App\Http\Controllers\Admin\AvatarController::class, 'update'])->name('update');
                        Route::post('/avatar', [App\Http\Controllers\Admin\AvatarController::class, 'delete'])->name('delete');
                    });

                    Route::resource('tags', App\Http\Controllers\Admin\TagController::class);
                    Route::post('/tagmassdelete', App\Http\Controllers\Admin\TagDeleteController::class)->name('tagdelete');

                    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
                    Route::post('/catmassdelete', [App\Http\Controllers\Admin\CategoryDeleteController::class, 'mass_delete'])->name('catdelete');

                    Route::resource('baseinfo', App\Http\Controllers\Admin\BaseinfoController::class);
                    Route::resource('posts.postmetas', App\Http\Controllers\Admin\PostPostmetaController::class);
                    Route::resource('posts.comments', App\Http\Controllers\Admin\PostCommentController::class);


                    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
                    Route::name('users.')->group(function() {
                        Route::post('/users/delete', [App\Http\Controllers\Admin\UserController::class, 'delete'])->name('forcedelete');
                        Route::post('/users/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('restore');
                        Route::post('/users/status', App\Http\Controllers\Admin\StatusController::class)->name('status');
                    });


                    Route::resource('posts', App\Http\Controllers\Admin\PostController::class);

                    Route::post('/hero_image', [App\Http\Controllers\Admin\ImageController::class, 'delete_heroimage'])->name('hero_image.delete');
                    Route::delete('/blog_image', [App\Http\Controllers\Admin\ImageController::class, 'delete_blogimage'])->name('blog_image.delete');
                    Route::post('/gallery_image', [App\Http\Controllers\Admin\ImageController::class, 'delete_galleryimage'])->name('gallery_image.delete');

                    Route::name('posts.')->group(function() {
                        Route::post('/posts/delete', [App\Http\Controllers\Admin\PostController::class, 'delete'])->name('forcedelete');
                        Route::post('/posts/restore', [App\Http\Controllers\Admin\PostController::class, 'restore'])->name('restore');
                        Route::post('/posts/status', App\Http\Controllers\Admin\PostStatusController::class)->name('status');
                        Route::post('/posts/favorite', App\Http\Controllers\Admin\PostFavoriteController::class)->name('favorite');
                    });

                    Route::resource('baseinfos', App\Http\Controllers\Admin\BaseinfoController::class)->only(['edit', 'update']);
                    // Route::get('/baseinfos/{id}/edit', [App\Http\Controllers\Admin\BaseinfoController::class, 'edit']);
                });
            });
        });
    });

    Route::prefix('author')->group(function() {
        Route::middleware('role:author|admin|super-admin')->group(function() {
            Route::name('author.')->group(function() {
                Route::get('/', [App\Http\Controllers\Author\DashboardController::class, 'index'])->name('index');
                // Users routes
                Route::resource('tags', App\Http\Controllers\Author\TagController::class)->only(['index', 'show']);
                Route::resource('categories', App\Http\Controllers\Author\CategoryController::class)->only(['index', 'show']);
                Route::resource('posts.postmetas', App\Http\Controllers\Author\PostPostmetaController::class);
                Route::resource('posts', App\Http\Controllers\Author\PostController::class);

                Route::post('/hero_image', [App\Http\Controllers\Author\ImageController::class, 'delete_heroimage'])->name('hero_image.delete');
                Route::post('/gallery_image', [App\Http\Controllers\Author\ImageController::class, 'delete_galleryimage'])->name('gallery_image.delete');

                Route::name('posts.')->group(function() {
                    Route::post('/posts/status', App\Http\Controllers\Author\PostStatusController::class)->name('status');
                });

                Route::resource('users', App\Http\Controllers\Author\UserController::class)->only(['edit', 'update']);

                Route::name('avatar.')->group(function() {
                    Route::put('/avatar', [App\Http\Controllers\Author\AvatarController::class, 'update'])->name('update');
                    Route::post('/avatar', [App\Http\Controllers\Author\AvatarController::class, 'delete'])->name('delete');
                });
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
