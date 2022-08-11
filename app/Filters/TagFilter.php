<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TagFilter extends QueryFilter
{
    public function search(?string $keyword = null)
    {
        return $this->builder->where(function(Builder $builder) use ($keyword) {
            return $builder->where('title', 'like', '%'.$keyword.'%')->orWhere('content', 'like', '%'.$keyword.'%'); });
    }

    public function tagTitle($order = 'asc')
    {
        return $this->builder->orderBy('title', $order);
    }

    public function tagContent($order = 'asc')
    {
        return $this->builder->orderBy('content', $order);
    }
}
