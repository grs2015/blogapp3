<?php

namespace App\Models\Builders;

use App\Enums\UserStatus;

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
        $this->model->save();
    }

    public function markAsPending(): void
    {
        $this->model->status = UserStatus::Pending;
        $this->model->save();
    }
}
