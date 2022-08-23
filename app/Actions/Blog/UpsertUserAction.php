<?php

namespace App\Actions\Blog;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\DataTransferObjects\UserData;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UpsertUserAction
{
    public static function execute(UserData $data, Request $request): User
    {
        $user = User::findOr($data->id, function() use ($data, $request) {
            $user = User::create([...$data->only('first_name', 'last_name', 'email', 'mobile')->all(),
                'password' => Hash::make($request['password'])]);
            $user->markAsDisabled();
            $user->assignRole('admin');
            $user['registered_at'] = now()->toDateString();
            return $user;
        });

        if ($data->email !== $user->email &&
            $user instanceof MustVerifyEmail) {
            self::updateVerifiedUser($user, $data);
        } else {
            $user->forceFill($data->only('first_name', 'last_name', 'email', 'mobile')->all())->save();
        }
        return $user;
    }

    protected static function updateVerifiedUser($user, UserData $data)
    {
        $user->forceFill([...$data->only('first_name', 'last_name', 'email', 'mobile')->all(),
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
