<?php

namespace App\Actions\Blog;

use App\Models\User;
use App\Services\ImageService;
use App\Http\Requests\AvatarRequest;
use Illuminate\Database\Eloquent\Model;

class UpdateAvatarImageAction
{
    public static function execute(AvatarRequest $request, ImageService $imageService): Model
    {
        $user = User::getEntityById($request->id);
        if ($request->file('avatar')) {
            if ($user->avatar) {
                $imageService->deleteAvatarImage($user->avatar);
            }
            $imageService->generateNames($request->file('avatar'));
            $avatar = $imageService->storeAvatarImage([[640, 480]])->avatarFilenamesDB;
        }

        $user['avatar'] = $avatar;
        $user->save();

        return $user;
    }
}
