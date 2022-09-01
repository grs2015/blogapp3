<?php

namespace App\ViewModels;

use App\Models\Post;
use Illuminate\Http\Request;
use App\DataTransferObjects\PostData;

class GetAuthorDashboardViewModel extends ViewModel
{
    public function __construct(
        public readonly Request $request
    ) {}

    public function recentPosts()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPending()->orderBy('published_at', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('id', 'status', 'title', 'published_at'));
    }

    public function draftPostsCount()
    {
        return Post::whereBelongsTo($this->request->user())->onlyDraft()->count();
    }

    public function pendingPostsCount()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPending()->count();
    }

    public function postsCount()
    {
        return Post::whereBelongsTo($this->request->user())->count();
    }

    public function publishedPostsCount()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPublished()->count();
    }

    public function mostRated()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPublished()->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('rating', 'title', 'published_at'));
    }

    public function mostViewed()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPublished()->orderBy('views', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('views', 'title', 'published_at'));
    }

    public function mostCommented()
    {
        return Post::whereBelongsTo($this->request->user())->onlyPublished()->withCount('comments')->orderBy('comments_count', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('comments_count', 'title', 'published_at'));
    }
}
