<?php

namespace App\Models\Builders;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends BaseBuilder
{
    public function markAsDisabled(): void
    {
        $this->model->status = UserStatus::Disabled;
        $this->model->save();
    }

    public function markAsEnabled(): void
    {
        $this->model->status = UserStatus::Enabled;
        $this->model->registered_at = now()->toDateString();
        $this->model->save();
    }

    public function markAsPending(): void
    {
        $this->model->status = UserStatus::Pending;
        $this->model->save();
    }

    public function onlyEnabled(): Builder
    {
        return $this->where('status', UserStatus::Enabled);
    }

    public function onlyPending(): Builder
    {
        return $this->where('status', UserStatus::Pending);
    }

    public function onlyAuthors(): Builder
    {
        return $this->whereHas('roles', fn(Builder $builder) => $builder->where('name', 'author'));
    }

    public function onlyMembers(): Builder
    {
        return $this->whereHas('roles', fn(Builder $builder) => $builder->where('name', 'member'));
    }
}
