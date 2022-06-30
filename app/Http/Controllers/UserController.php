<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function index()
    {
        $users = $this->userRepository->getAllEntries();

        return view('user.index', compact(['users']));
    }

    public function show(User $user)
    {
        $user = $this->userRepository->getEntryById($user->id);

        return view('user.show', compact(['user']));
    }

    public function destroy(User $user)
    {

    }
}
