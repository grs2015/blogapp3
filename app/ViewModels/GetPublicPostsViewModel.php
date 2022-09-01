<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPublicPostsViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService,
        public Request $request,
    ) {}

    public function posts(): LengthAwarePaginator
    {

        $items =  Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function()  {
            return Post::onlyPublished()->orderByDesc('published_at')->with(['user:id,email,first_name,last_name,full_name'])->get();})
            ->map(function($item) { return PostData::from($item)->only('id', 'slug', 'time_to_read', 'title', 'summary', 'published_at', 'views', 'rating', 'user.full_name'); });

            // $items =  Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function()  {
            //     return Post::withCount('comments')->whereBelongsTo($this->request->user())->filter($this->filters)->with(['user:id,email,first_name,last_name,full_name'])->get();})
            //     ->map
            //     ->getData();


        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;


        if ($this->request->query())
        {
            if ($this->request->query('per_page')) {
                $perPage = (int)$this->request->query('per_page');
            } else {
                $perPage = $items->count();
            }
        }

        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $items->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return $paginated->appends(request()->query());
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
