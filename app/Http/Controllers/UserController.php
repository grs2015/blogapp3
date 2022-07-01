<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
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

        // $users = $this->userRepository->getAllEntries();

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
}
