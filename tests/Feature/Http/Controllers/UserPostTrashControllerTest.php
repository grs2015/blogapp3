<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\Trash\UserPostTrashController;


uses()->group('TrashController');

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->posts = Post::factory()->
        for($this->user)->
        has(Tag::factory()->count(1))->
        has(Category::factory()->count(1))->
        count(2)->
        create();
    $this->postIds = $this->posts->pluck('id')->toArray();
});
/* ----------------------------- @destroy methofd ---------------------------- */
it('forces to delete the trashed entries given by array of IDs as well as related models 1-M and entry in pivot-table', function() {

    $this->delete(action([UserPostController::class, 'destroy'], ['user' => $this->user->id, 'post' => $this->posts[0]->slug]));
    $this->delete(action([UserPostController::class, 'destroy'], ['user' => $this->user->id, 'post' => $this->posts[1]->slug]));

    $deletedAt = Post::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($this->posts[0]);
    $this->assertSoftDeleted($this->posts[1]);
    $this->assertDatabaseHas('posts', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([UserPostTrashController::class, 'destroy'], ['user' => $this->user->id]), ['ids' => $this->postIds]);

    $this->assertDataBaseMissing('posts', ['id' => $this->posts[0]->id, 'id' => $this->posts[1]->id]);
    $this->assertModelMissing($this->posts[0]);
    $this->assertModelMissing($this->posts[1]);
    $this->assertDatabaseMissing('posts', ['deleted_at' => $deletedAt]);

    $this->assertDatabaseMissing('post_tag', [
        'post_id' => $this->posts[0]->id,
        'post_id' => $this->posts[1]->id,
        'tag_id' => Tag::first()->id
    ]);
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $this->posts[0]->id,
        'post_id' => $this->posts[1]->id,
        'category_id' => Category::first()->id
    ]);

    $response->assertRedirect(route('users.posts.index', ['user' => $this->user->id]));

});

it('restores the trashed entries given by array of IDs', function() {

    $this->delete(action([UserPostController::class, 'destroy'], ['user' => $this->user->id, 'post' => $this->posts[0]->slug]));
    $this->delete(action([UserPostController::class, 'destroy'], ['user' => $this->user->id, 'post' => $this->posts[1]->slug]));

    $deletedAt = Post::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($this->posts[0]);
    $this->assertSoftDeleted($this->posts[1]);
    $this->assertDatabaseHas('posts', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([UserPostTrashController::class, 'restore'], ['user' => $this->user->id]), ['ids' => $this->postIds]);

    $this->assertDataBaseHas('posts', ['id' => $this->posts[0]->id, 'id' => $this->posts[1]->id]);
    $this->assertModelExists($this->posts[0]);
    $this->assertModelExists($this->posts[1]);
    $this->assertDatabaseMissing('posts', ['deleted_at' => $deletedAt]);


    $response->assertRedirect(route('users.posts.index', ['user' => $this->user->id]));
});
