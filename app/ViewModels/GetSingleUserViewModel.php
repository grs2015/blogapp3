<?php

namespace App\ViewModels;

use App\Models\User;
use App\DataTransferObjects\UserData;




class GetSingleUserViewModel extends ViewModel
{
    public function __construct(
        public readonly User $user
    ) {}

    public function user(): UserData
    {
        return UserData::from($this->user->withCount('posts'));
    }
}
