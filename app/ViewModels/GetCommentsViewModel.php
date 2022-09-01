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
        public Post $post
    ) {  }

    public function comments(): ?Collection
    {
        if (!$this->post) {
            return null;
        }

        return Comment::whereBelongsTo($this->post)->get()->map->getData();
    }
}
