<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CatFilter extends QueryFilter
{
    public function search(?string $keyword = null)
    {
        return $this->builder->where(function(Builder $builder) use ($keyword) {
            return $builder->where('title', 'like', '%'.$keyword.'%')->orWhere('content', 'like', '%'.$keyword.'%'); });
    }

    public function catTitle($order = 'asc')
    {
        return $this->builder->orderBy('title', $order);
    }

    public function catContent($order = 'asc')
    {
        return $this->builder->orderBy('content', $order);
    }

    public function catIcon($order = 'asc')
    {
        return $this->builder->orderBy('icon', $order);
    }
}
