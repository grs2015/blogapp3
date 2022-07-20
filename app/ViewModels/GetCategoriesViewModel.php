<?php

namespace App\ViewModels;

use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetCategoriesViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService
    ) {}

    public function categories(): Collection
    {
        return Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() {
            return Category::all();})
            ->map
            ->getData();
    }
}
