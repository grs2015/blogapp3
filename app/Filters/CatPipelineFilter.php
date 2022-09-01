<?php

namespace App\Filters;

use Closure;
use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;


class CatPipelineFilter extends Filter
{
    public function handle(Builder $posts, Closure $next): Builder
    {
        if (count($this->ids) === 0) {
            return $next($posts);
        }

        $posts->whereHas('categories', fn(Builder $cats) => $cats->whereIn('id', $this->ids));

        return $next($posts);
    }
}
