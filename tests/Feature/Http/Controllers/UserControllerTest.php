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
