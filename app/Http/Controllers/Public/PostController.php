<?php

namespace App\Http\Controllers\Public;

use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Http\Controllers\Controller;
use App\ViewModels\GetPublicPostsViewModel;
use Illuminate\Database\Eloquent\Collection;
use App\ViewModels\GetPublicSinglePostViewModel;
use App\ViewModels\GetPublicFilteredPostsViewModel;

class PostController extends Controller
{
    public function index(Request $request, CacheService $cacheService)
    {
        return Inertia::render('Public/Index', [
            'model' => new GetPublicPostsViewModel($cacheService, $request)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, CacheService $cacheService): Response
    {
        if (!in_array($post->id, session()->get('viewed_posts', []) )) {
            if (!$post['views']) {
                $post['views'] = 0;
            }
            $post->increment('views');
            session()->push('viewed_posts', $post->id);
            $post->save();
        }

        return Inertia::render('Public/Show', [
            'model' => new GetPublicSinglePostViewModel($post, $cacheService)
        ]);
    }
}
