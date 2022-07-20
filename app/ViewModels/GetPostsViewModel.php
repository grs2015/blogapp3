<?php

namespace App\ViewModels;

use App\Models\Post;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;
use Illuminate\Support\Facades\Cache;


class GetPostsViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService
    ) {}

    /**
     * @return Collection<PostData>
     */
    public function posts(): Collection
    {
        return Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() {
            return Post::with(['user', 'tags', 'categories'])->get();})
            ->map
            ->getData();
    }
}
