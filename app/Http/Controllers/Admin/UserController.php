<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Filters\UserFilter;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Events\UserRoleUpdated;
use App\Events\UserStatusUpdated;
use App\Http\Requests\TrashRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\ViewModels\GetUsersViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\ViewModels\UpsertUserViewModel;
use App\Http\Requests\UpdateUserRequest;
use App\ViewModels\GetSingleUserViewModel;
use App\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
        // $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request, UserFilter $filters)
    {
        return Inertia::render('User/Index', [
            'model' => new GetUsersViewModel($request, $filters)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('User/Form', [
            'model' => new UpsertUserViewModel()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('User/Form', [
            'model' => new UpsertUserViewModel($user)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return Inertia::render('User/Show', [
            'model' => new GetSingleUserViewModel($user)
        ]);
    }

    /**
     * Delete model
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userRepository->deleteEntry($user->id);

        return redirect()->action([UserController::class, 'index']);
    }

    /**
     * Force to delete trashed models
     *
     * @param TrashRequest $request
     * @return RedirectResponse
     */
    public function delete(TrashRequest $request): RedirectResponse
    {
        $this->authorize('forceDelete', User::class);

        $userToDelete = $this->userRepository->forceDeleteEntry($request->ids);

        $userToDelete->each(function($item) {
            $item->forceDelete();
        });

        return redirect()->action([UserController::class, 'index']);
    }

    /**
     * Restore trashed models
     *
     * @param TrashRequest $request
     * @return RedirectResponse
     */
    public function restore(TrashRequest $request): RedirectResponse
    {
        $this->authorize('restore', User::class);

        $this->userRepository->restoreEntry($request->ids);

        return redirect()->action([UserController::class, 'index']);
    }

    // public function update(UpdateUserRequest $request, User $user): RedirectResponse
    // {
    //     $validated = $request->validated();

    //     if ($user->status != $validated['status']) {
    //         $user->status = $validated['status'];
    //         UserStatusUpdated::dispatch($user);
    //     }

    //     $user->syncRoles($validated['roles']);
    //     UserRoleUpdated::dispatch($user);

    //     return redirect()->action([UserController::class, 'index']);
    // }

    // public function create()
    // {
    //     return view('admin.user.create');
    // }

    // public function edit(User $user)
    // {
    //     $user = $this->userRepository->getEntryById($user->id);

    //     return view('admin.user.create', compact('user'));
    // }

    // public function store(Request $request) {

    // }
}
