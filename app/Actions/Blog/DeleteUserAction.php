<?php

namespace App\Actions\Blog;

use App\Models\User;
use App\Enums\UserStatus;
use App\Exceptions\CannotDeleteUser;

class DeleteUserAction
{
    public static function execute(User $user): bool
    {
        if ($user->hasRole('super-admin')) {
            throw CannotDeleteUser::because('User is super-admin');
        }

        if ($user->hasAnyRole('author', 'member') && !($user->status === UserStatus::Pending)) {
            throw CannotDeleteUser::because('User is an activated author/member');
        }

        return $user->delete();
    }
}
