<?php

use App\Models\User;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Trash\UserTrashController;

uses()->group('UserTrash');

beforeEach(function() {
    $this->users = User::factory()->count(2)->create();
    $this->userIds = $this->users->pluck('id')->toArray();
});

/* ----------------------------- @destroy method ---------------------------- */
it('forces to delete the trashed entries given by array of IDs', function() {

    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[0]->id]));
    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[1]->id]));

    $deletedAt = User::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($this->users[0]);
    $this->assertSoftDeleted($this->users[1]);
    $this->assertDatabaseHas('users', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([UserTrashController::class, 'destroy']), ['ids' => $this->userIds]);

    $this->assertDataBaseMissing('users', ['id' => $this->users[0]->id, 'id' => $this->users[1]->id]);
    $this->assertModelMissing($this->users[0]);
    $this->assertModelMissing($this->users[1]);
    $this->assertDatabaseMissing('users', ['deleted_at' => $deletedAt]);

    $response->assertRedirect(route('users.index'));
});

it('restores the trashed entries given by array of IDs', function() {

    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[0]->id]));
    $this->delete(action([UserController::class, 'destroy'], ['user' => $this->users[1]->id]));

    $deletedAt = User::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($this->users[0]);
    $this->assertSoftDeleted($this->users[1]);
    $this->assertDatabaseHas('users', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([UserTrashController::class, 'restore']), ['ids' => $this->userIds]);

    $this->assertDataBaseHas('users', ['id' => $this->users[0]->id, 'id' => $this->users[1]->id]);
    $this->assertModelExists($this->users[0]);
    $this->assertModelExists($this->users[1]);
    $this->assertDatabaseMissing('users', ['deleted_at' => $deletedAt]);


    $response->assertRedirect(route('users.index'));
});
