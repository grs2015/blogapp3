<?php

namespace App\Http\Controllers\Public;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\PostData;
use App\Actions\Blog\FilterPostAction;
use App\DataTransferObjects\FilterData;
use App\ViewModels\GetPublicFilteredPostsViewModel;

class FilterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(FilterData $data, CacheService $cacheService)
    {
        $res = FilterPostAction::execute($data);

        return Inertia::render('Public/Filter', [
            'model' => new GetPublicFilteredPostsViewModel($cacheService, $res)
        ]);
    }
}
