<?php

namespace App\ViewModels;

use App\Models\User;
use App\Filters\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUsersViewModel extends ViewModel
{
    public function __construct(
        public Request $request,
        public UserFilter $filters
    ) {}

    public function users(): LengthAwarePaginator
    {
        $items = User::withCount('posts')->filter($this->filters)->get()->map->getData();

        if (!Auth::user()->hasRole('super-admin')) {
            $items = $items->reject(fn($user) => $user->roles === 'super-admin');
        }

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
