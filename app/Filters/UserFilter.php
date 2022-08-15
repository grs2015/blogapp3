<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends QueryFilter
{
    public function search(?string $keyword = null)
    {
        return $this->builder;
        return $this->builder->where(function(Builder $builder) use ($keyword) {
            return $builder->where('first_name', 'like', '%'.$keyword.'%')
                ->orWhere('last_name', 'like', '%'.$keyword.'%')
                ->orWhere('full_name', 'like', '%'.$keyword.'%')
                ->orWhere('registered_at', 'like', '%'.$keyword.'%')
                ->orWhere('last_login', 'like', '%'.$keyword.'%');
        });
    }

    public function userFirstname($order = 'asc')
    {
        return $this->builder->orderBy('first_name', $order);
    }

    public function userLastname($order = 'asc')
    {
        return $this->builder->orderBy('last_name', $order);
    }

    public function userFullname($order = 'asc')
    {
        return $this->builder->orderBy('full_name', $order);
    }

    public function userRole($order = 'asc')
    {
        return $this->builder->whereHas('roles', function(Builder $builder) use ($order) {
            $builder->orderBy('name', $order);
        });
    }

    public function userStatus($order = 'asc')
    {
        return $this->builder->orderBy('status', $order);
    }

    public function userRegistered($order = 'asc')
    {
        return $this->builder->orderBy('registered_at', $order);
    }

    public function userLastLoggedin($order = 'asc')
    {
        return $this->builder->orderBy('last_login', $order);
    }

    public function userPostcount($order = 'asc')
    {
        return $this->builder->orderBy('posts_count', $order);
    }
}
