<?php

namespace App\Providers;

use App\Repositories\TagRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\BaseinfoRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\PostmetaRepository;
use App\Interfaces\TagRepositoryInterface;
use App\Repositories\PublicPostRepository;
use App\Interfaces\PostRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\CommentRepositoryInterface;
use App\Interfaces\BaseinfoRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PostmetaRepositoryInterface;
use App\Interfaces\PublicPostRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(PostmetaRepositoryInterface::class, PostmetaRepository::class);
        $this->app->bind(BaseinfoRepositoryInterface::class, BaseinfoRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PublicPostRepositoryInterface::class, PublicPostRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
