<?php

namespace App\Http\Controllers\Trash;

use App\Models\Post;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PostTrashRequest;
use App\Http\Controllers\UserPostController;
use App\Interfaces\PostRepositoryInterface;

final class UserPostTrashController extends Controller
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function destroy(User $user, PostTrashRequest $request):RedirectResponse
    {
        $postToDelete = $this->postRepository->forceDeleteEntry($request->ids);
        $postToDelete->each(function($item) {
            $item->tags()->detach();
            $item->categories()->detach();
            $item->forceDelete();
        });

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);
    }

    public function restore(User $user, PostTrashRequest $request):RedirectResponse
    {
        $this->postRepository->restoreEntry($request->ids);

        return redirect()->action([UserPostController::class, 'index'], ['user' => $user->id]);
    }
}
