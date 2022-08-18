<?php

namespace App\Actions\Blog;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;

class DeleteAvatarImageAction
{
    public static function execute(Request $request, ImageService $imageService): Model
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:users,id']
        ]);

        $user = User::getEntityById($request->id);

        if ($user->avatar) {
            $imageService->deleteAvatarImage($user->avatar);
        }

        $user->avatar = null;
        $user->save();

        return $user;
    }
}
