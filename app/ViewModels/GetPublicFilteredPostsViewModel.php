<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;
use App\Enums\PostStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPublicFilteredPostsViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService,
        public Collection $collection,
    ) {}

    public function posts(): ?LengthAwarePaginator
    {
        $items = Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function()  {
            return $this->collection->filter(fn($item) => $item->status === PostStatus::Published)->sortByDesc('published_at')->each(fn($item) => $item->load(['user:id,email,first_name,last_name,full_name'])); })
            ->map(fn($item) => PostData::from($item)->only('id', 'slug', 'time_to_read', 'title', 'summary', 'published_at', 'views', 'rating', 'user.full_name'))  ;

        if (count($items) === 0 ) {
            return null;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $items->count();

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


