<?php

namespace App\ViewModels;

use App\Models\User;
use App\DataTransferObjects\UserData;

class UpsertUserViewModel extends ViewModel
{
    public function __construct(
        public readonly ?User $user = null
    ) {}

    public function user(): ?UserData
    {
        if (!$this->user) {
            return null;
        }

        return UserData::from($this->user->loadCount('posts'));
    }
}
