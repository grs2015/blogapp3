<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Events\UserRoleUpdated;
use App\Events\UserStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\UpdateUserRequest;
use App\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function index(CacheService $cacheService)
    {
        $users = Cache::remember($cacheService->cacheResponse(), $cacheService->cacheTime(), function() {
            return $this->userRepository->getAllEntries();
        });

        return view('user.index', compact(['users']));
    }

    public function show(User $user)
    {
        $user = $this->userRepository->getEntryById($user->id);

        return view('user.show', compact(['user']));
    }

    public function destroy(User $user)
    {
        $this->userRepository->deleteEntry($user->id);

        return redirect()->action([UserController::class, 'index']);
    }

    public function update(UpdateUserRequest $request, User $user):RedirectResponse
    {
        $validated = $request->validated();

        if ($user->status != $validated['status']) {
            $user->status = $validated['status'];
            UserStatusUpdated::dispatch($user);
        }

        $user->syncRoles($validated['roles']);
        UserRoleUpdated::dispatch($user);

        return redirect()->action([UserController::class, 'index']);
    }
}
