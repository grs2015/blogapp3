<?php

namespace App\ViewModels;

use App\Models\Category;
use App\Filters\CatFilter;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class GetCategoriesViewModel extends ViewModel
{
    public function __construct(
        public CacheService $cacheService,
        public Request $request,
        public CatFilter $filters
    ) {}

    public function categories(): LengthAwarePaginator
    {
        $items = Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function() {
            return Category::filter($this->filters)->get();})
            ->map
            ->getData();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;


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

    public function sorting(): array
    {
        return array("column" => $this->request->query('column'), "descending" => $this->request->query('descending'));
    }
}
