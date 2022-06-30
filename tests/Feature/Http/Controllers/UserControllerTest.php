<?php

use App\Models\User;
use App\Http\Controllers\UserController;

uses()->group('UC');

/* ------------------------------ @index method ----------------------------- */
it('renders the user page with users data', function() {
    $users = User::factory()->count(5)->create();
    $userEmail = User::inRandomOrder()->first()->email;

    $response = $this->get(action([UserController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All users:');
    $response->assertSee($userEmail);
});

/* ------------------------------ @show method ------------------------------ */
it('renders single user entry by given ID', function() {
    $user = User::factory()->create();

    $response = $this->get(action([UserController::class, 'show'], ['user' => $user->id]));

    $response->assertSee($user->first_name);
    $response->assertSee($user->last_name);
    $response->assertSee($user->email);
    $response->assertDontSee($user->summary);
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry', function() {
    $user = User::factory()->create();

    $response = $this->delete(action([UserController::class, 'destroy'], ['user' => $user->id]));

    $response->assertRedirect(route('users.index'));
    $this->assertModelMissing($user);
    $this->assertDatabaseMissing('users', $user->toArray());
});
