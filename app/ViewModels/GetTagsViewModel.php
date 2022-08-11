<?php

namespace App\ViewModels;

use App\Models\Tag;
use App\Filters\TagFilter;
use Illuminate\Http\Request;
use App\ViewModels\ViewModel;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTagsViewModel extends ViewModel
{
    public function __construct(
        public Request $request,
        public TagFilter $filters
    ) {}

    public function tags(): LengthAwarePaginator
    {
        $items = Tag::filter($this->filters)->get()->map->getData();

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
