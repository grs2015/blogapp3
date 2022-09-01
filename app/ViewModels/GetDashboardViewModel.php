<?php

namespace App\ViewModels;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\DataTransferObjects\PostData;
use App\DataTransferObjects\UserData;
use Illuminate\Database\Eloquent\Builder;

class GetDashboardViewModel extends ViewModel
{
    public function __construct(
        public readonly Request $request
    ) {}

    public function activeAuthors()
    {
        return User::onlyEnabled()->onlyAuthors()->withCount('posts')->where('posts_count', '>', 0)->orderBy('posts_count')->take(10)->get()
            ->map(fn($user) => UserData::from($user)->only('id', 'email', 'full_name', 'last_login', 'roles', 'posts_count'));
    }

    public function countAuthorUsers()
    {
        return User::onlyEnabled()->onlyAuthors()->count();
    }

    public function countMemberUsers()
    {
        return User::onlyEnabled()->onlyMembers()->count();
    }

    public function enabledUsersCount()
    {
        return User::onlyEnabled()->whereHas('roles',
            fn(Builder $builder) => $builder->where('name','<>', 'admin')->where('name','<>','super-admin'))->count();
    }

    public function pendingUsersCount()
    {
        return User::onlyPending()->count();
    }

    public function recentUsers()
    {
        return User::onlyPending()->orderBy('created_at', 'desc')->take(10)->get()->map(fn($user) => UserData::from($user)->only('id', 'status', 'full_name', 'roles', 'created_at', 'email'));
    }

/* ----------------------------- Post statistics ---------------------------- */

    public function recentPosts()
    {
        return Post::onlyPending()->orderBy('published_at', 'desc')->with(['user:id,first_name,email,full_name'])->take(10)->get()->map(fn($post) => PostData::from($post)->only('id', 'status', 'title', 'published_at', 'user.email', 'user.full_name'));
    }

    public function draftPostsCount()
    {
        return Post::onlyDraft()->count();
    }

    public function pendingPostsCount()
    {
        return Post::onlyPending()->count();
    }

    public function postsCount()
    {
        return Post::count();
    }

    public function publishedPostsCount()
    {
        return Post::onlyPublished()->count();
    }

    public function mostRated()
    {
        return Post::onlyPublished()->with(['user:id,first_name,email,full_name'])->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('rating', 'title', 'published_at', 'user.email', 'user.full_name'));
    }

    public function mostViewed()
    {
        return Post::onlyPublished()->with(['user:id,first_name,email,full_name'])->orderBy('views', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('views', 'title', 'published_at', 'user.email', 'user.full_name'));
    }

    public function mostCommented()
    {
        return Post::onlyPublished()->with(['user:id,first_name,email,full_name'])->withCount('comments')->orderBy('comments_count', 'desc')->take(10)->get()->map(fn($post) => PostData::from($post)->only('comments_count', 'title', 'published_at', 'user.email', 'user.full_name'));
    }
}
