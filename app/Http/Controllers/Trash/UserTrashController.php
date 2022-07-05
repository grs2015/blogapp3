<?php

namespace App\Http\Controllers\Trash;

use App\Models\User;
use App\Http\Requests\TrashRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\UserController;
use App\Interfaces\UserRepositoryInterface;

final class UserTrashController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function destroy(TrashRequest $request):RedirectResponse
    {
        $userToDelete = $this->userRepository->forceDeleteEntry($request->ids);

        $userToDelete->each(function($item) {
            $item->forceDelete();
        });

        return redirect()->action([UserController::class, 'index']);
    }

    public function restore(TrashRequest $request):RedirectResponse
    {
        $this->userRepository->restoreEntry($request->ids);

        return redirect()->action([UserController::class, 'index']);
    }
}
