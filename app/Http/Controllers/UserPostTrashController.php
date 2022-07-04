<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostTrashRequest;

class UserPostTrashController extends Controller
{
    public function destroy(User $user, PostTrashRequest $request):RedirectResponse
    {
        $postToDelete = Post::onlyTrashed()->whereIn('id', $request->ids)->get();
        $postToDelete->each(function($item) {
            $item->tags()->detach();
            $item->categories()->detach();
            $item->forceDelete();
        });

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);
    }

    public function restore(User $user, PostTrashRequest $request):RedirectResponse
    {
        Post::onlyTrashed()->whereIn('id', $request->ids)->restore();

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);
    }
}
