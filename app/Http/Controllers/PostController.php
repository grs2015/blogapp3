<?php

namespace App\Http\Controllers;

use App\Interfaces\PublicPostRepositoryInterface;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

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

        return response()->view('post.public.show', compact('post'))->withCookie(cookie('user', 'works', 5))->withHeaders(['SSS' => 'SSS']);
    }
}
