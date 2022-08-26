<?php

namespace App\Providers;

use App\Models\Baseinfo;
use App\Services\ImageService;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\BaseinfoController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Baseinfo::firstOrCreate(['id' => 1]);
    }
}
