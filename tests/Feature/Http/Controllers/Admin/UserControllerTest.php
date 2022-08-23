<?php

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses()->group('admin');
beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the user/index page with users data', function () {
    User::factory()->count(5)->create();
    $userEmail = User::first()->email;

    $response = $this->get(action([UserController::class, 'index']));

    $response->assertOk();
    $response->assertInertia(
        fn(Assert $page) => $page
            ->component('User/Index')
            ->has(
                'model',
                fn(Assert $page) => $page
                    ->has('users', 13)
                    ->has('users', fn(Assert $page) => $page
                            ->has('data', 6) // Not 5 because the one - initial (in beforeEach) already has been created
                            ->has('data.0', fn(Assert $page) => $page
                                    ->has('email')
                                    ->where('email', $userEmail)
                                    ->etc())
                            ->etc())
                    ->etc()
            )
    );
});

/* ------------------------------ @index method ----------------------------- */
it('logged-in as admin, renders the user page with users data but without super-admin data', function () {
    // Authorized access as admin
    User::factory()->count(5)->create();
    $userEmail = User::inRandomOrder()->first()->email;
    $saUser = User::factory()->create();
    $saUser->assignRole('super-admin');
    $saUserEmail = $saUser->email;

    $response = $this->get(action([UserController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All users:');
    $response->assertSee($userEmail);
    $response->assertDontSee($saUserEmail);

    // Unauthorized access
    loginAsAuthor();

    $response = $this->get(action([UserController::class, 'index']));

    $response->assertStatus(403);
});

it('logged-in as super-admin, renders the user page with users data with super-admin data', function () {
    // Authorized access as SA
    loginAsSuperAdmin();
    User::factory()->count(5)->create();
    $userEmail = User::inRandomOrder()->first()->email;
    $saUser = User::factory()->create();
    $saUser->assignRole('super-admin');
    $saUserEmail = $saUser->email;

    $response = $this->get(action([UserController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All users:');
    $response->assertSee($userEmail);
    $response->assertSee($saUserEmail);
});

/* ------------------------------ @show method ------------------------------ */
it('logged-in as admin, renders single user entry by given ID, cannot see super-admin data', function () {

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->get(action([UserController::class, 'show'], ['user' => $user->id]));

    $response->assertStatus(403);
});

it('logged-in as super-admin, renders single user entry by given ID, can see super-admin data', function () {
    loginAsSuperAdmin();
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->get(action([UserController::class, 'show'], ['user' => $user->id]));

    $response->assertSee($user->first_name);
    $response->assertSee($user->last_name);
    $response->assertSee($user->email);
    $response->assertDontSee($user->summary);
});

/* ----------------------------- @destroy method ---------------------------- */
it('logged-in as admin, checks deletion of user entry by given ID, except super-admin', function () {
    $user = User::factory()->create();
    $user->assignRole('author');

    $response = $this->delete(action([UserController::class, 'destroy'], ['user' => $user->id]));

    $response->assertRedirect(route('admin.users.index'));
    $this->assertSoftDeleted($user);
});

it('logged-in as admin, checks deletion of user entry by given ID, cannot delete super-admin', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->delete(action([UserController::class, 'destroy'], ['user' => $user->id]));

    $response->assertStatus(403);
});

/* ----------------------------- @restore method ---------------------------- */
it('logged-in as admin, restores the trashed entries given by array of IDs', function () {
    $this->users = User::factory()->count(2)->create();
    $this->userIds = $this->users->pluck('id')->toArray();

    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[0]->id]));
    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[1]->id]));

    $deletedAt = User::withTrashed()->get()->last()->deleted_at;
    $this->assertSoftDeleted($this->users[0]);
    $this->assertSoftDeleted($this->users[1]);
    $this->assertDatabaseHas('users', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([UserController::class, 'restore']), ['ids' => $this->userIds]);

    $this->assertDataBaseHas('users', ['id' => $this->users[0]->id, 'id' => $this->users[1]->id]);
    $this->assertModelExists($this->users[0]);
    $this->assertModelExists($this->users[1]);
    $this->assertDatabaseMissing('users', ['deleted_at' => $deletedAt]);

    $response->assertRedirect(route('admin.users.index'));
});

it('logged-in as super-admin, forces to delete the trashed entries given by array of IDs', function () {
    $this->users = User::factory()->count(2)->create();
    $this->userIds = $this->users->pluck('id')->toArray();

    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[0]->id]));
    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[1]->id]));

    $deletedAt = User::withTrashed()->get()->last()->deleted_at;
    $this->assertSoftDeleted($this->users[0]);
    $this->assertSoftDeleted($this->users[1]);
    $this->assertDatabaseHas('users', ['deleted_at' => $deletedAt]);

    loginAsSuperAdmin();

    $response = $this->post(action([UserController::class, 'delete']), ['ids' => $this->userIds]);

    $this->assertDataBaseMissing('users', ['id' => $this->users[0]->id, 'id' => $this->users[1]->id]);
    $this->assertModelMissing($this->users[0]);
    $this->assertModelMissing($this->users[1]);
    $this->assertDatabaseMissing('users', ['deleted_at' => $deletedAt]);

    $response->assertRedirect(route('admin.users.index'));
});
