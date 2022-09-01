<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function __construct(
        protected readonly array $ids
    ) {}

    abstract public function handle(Builder $posts, Closure $next): Builder;
}
