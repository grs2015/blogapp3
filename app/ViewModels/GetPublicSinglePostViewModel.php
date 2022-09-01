<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use App\Enums\CommentStatus;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;
use Illuminate\Support\Facades\Cache;

class GetPublicSinglePostViewModel extends ViewModel
{
    public function __construct(
        public readonly Post $post,
        public readonly CacheService $cacheService
    ) {}

    public function post(): PostData
    {
        $postCommentsPublished = $this->post->load(['comments' => function($query) {
            $query->where('status', CommentStatus::Published)->latest('published_at');
        }]);

        return Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() use ($postCommentsPublished)  {
            return PostData::from($postCommentsPublished->load('galleries', 'tags', 'categories', 'user')); });
    }

    public function tags(): Collection
    {
        return Tag::all()->map->getData();
    }

    public function categories(): Collection
    {
        return Category::all()->map->getData();
    }
}
