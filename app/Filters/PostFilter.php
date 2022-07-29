<?php

namespace App\Filters;

use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;

class PostFilter extends QueryFilter
{
    public function search(?string $keyword = null)
    {
        return $this->builder->where(function(Builder $builder) use ($keyword) {
            return $builder->where('title', 'like', '%'.$keyword.'%')
                ->orWhere('status', 'like', '%'.$keyword.'%')
                ->orWhere('favorite', 'like', '%'.$keyword.'%')
                ->orWhere(function(Builder $builder) use ($keyword) {
                    return $builder->whereHas('user', function(Builder $builder) use ($keyword) {
                        return $builder->where('first_name', 'like', '%'.$keyword.'%');
                    });
                });
        });
    }

    public function postTitle($order = 'asc')
    {
        return $this->builder->orderBy('title', $order);
    }

    public function postStatus($order = 'asc')
    {
        return $this->builder->orderBy('status', $order);
    }

    public function postFavorite($order = 'asc')
    {
        return $this->builder->orderBy('favorite', $order);
    }

    public function postViews($order = 'asc')
    {
        return $this->builder->orderBy('views', $order);
    }
}
