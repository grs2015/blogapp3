<?php

namespace App\ViewModels;

use App\Models\Post;
use App\Filters\PostFilter;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PostData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;


class GetPostsViewModel extends ViewModel
{
    private const PER_PAGE = 20;

    public function __construct(
        public CacheService $cacheService,
        public Request $request,
        public PostFilter $filters
    ) {}

    /**
     *
     */
    public function posts(): LengthAwarePaginator
    {
        $items =  Cache::remember($this->cacheService->cacheResponse(), $this->cacheService->cacheTime(), function()  {
            return Post::with(['user:id,email,first_name'])->filter($this->filters)->get();})
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
