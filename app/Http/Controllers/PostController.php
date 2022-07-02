<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use App\Interfaces\PublicPostRepositoryInterface;

class PostController extends Controller
{
    public function __construct(
        private PublicPostRepositoryInterface $publicPostRepository
    ) {  }

    public function index(CacheService $cacheService)
    {
        $posts = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() {
            return $this->publicPostRepository->getAllEntries();
        });

        return response()->view('post.public.index', compact(['posts']));
    }

    public function show(Post $post, CacheService $cacheService, Request $request)
    {
        $post = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() use ($post) {
            return $this->publicPostRepository->getEntryById($post->id);
        });

        if (!in_array($post->id, session()->get('viewed_posts', []) )) {
            $post->increment('views');
            session()->push('viewed_posts', $post->id);
        }

        $views = Post::whereId($post->id)->first()->views;

        return response()->view('post.public.show', compact('post', 'views'));
    }
}
