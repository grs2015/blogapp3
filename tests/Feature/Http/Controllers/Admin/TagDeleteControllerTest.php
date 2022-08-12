<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TagDeleteController;

uses()->group('admin', 'tagdelete');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});

/* ------------------------------- @invokabe class  ------------------------------- */
it('checks the mass deletion of tag entry as well as entries in pivot tables', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Tag::factory()->count(1))
        ->has(Category::factory()->count(1))
        ->for($user)
        ->create(['title' => 'New Tag Entry']);
    // Assertion #1
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    $this->assertDatabaseHas('post_tag', ['tag_id' => Tag::first()->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('tags', ['id' => Tag::first()->id, 'title' => Tag::first()->title]);
    $tag = Tag::first();

    $response = $this->post(action(TagDeleteController::class, ['data' => array(Tag::first()->id)]));

    $response->assertRedirect(action([TagController::class, 'index']));
    $this->assertModelMissing($tag);
    $this->assertDatabaseMissing('tags', $tag->toArray());
    $this->assertDatabaseMissing('post_tag', [
        'post_id' => $post->id,
        'tag_id' => $tag->id
    ]);
});
