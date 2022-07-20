<?php

namespace App\ViewModels;

use App\Models\Post;
use App\Models\Comment;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\DataTransferObjects\CommentData;

class GetCommentsViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService,
        public Post $post
    ) {  }

    public function comments(): ?Collection
    {
        return Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() {
            return Comment::whereBelongsTo($this->post)->get(); })
            ->map(fn(Comment $comment) => CommentData::from($comment));
    }
}
