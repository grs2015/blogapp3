<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class TagPipelineFilter extends Filter
{
    public function handle(Builder $posts, Closure $next): Builder
    {
        if (count($this->ids) === 0) {
            return $next($posts);
        }

        $posts->whereHas('tags', fn(Builder $tags) => $tags->whereIn('id', $this->ids));

        return $next($posts);
    }
}
