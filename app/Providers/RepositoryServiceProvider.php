<?php

namespace App\Providers;

use App\Repositories\TagRepository;
use App\Repositories\PostRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\PostRepositoryInterface;

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
