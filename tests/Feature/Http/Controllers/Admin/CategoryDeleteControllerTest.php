<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryDeleteController;
use App\Http\Controllers\Admin\CategoryMassDeleteController;

uses()->group('admin', 'catdelete');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});

/* ------------------------------- @mass_delete method ------------------------------- */
it('checks the mass deletion of cat entry as well as entries in pivot tables', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(1))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    // Assertion #1
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    $this->assertDatabaseHas('category_post', ['category_id' => Category::first()->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('categories', ['id' => Category::first()->id, 'title' => Category::first()->title]);
    $cat = Category::first();

    $response = $this->post(action([CategoryDeleteController::class, 'mass_delete'], ['data' => array(Category::first()->id)]));

    $response->assertRedirect(action([CategoryController::class, 'index']));
    $this->assertModelMissing($cat);
    $this->assertDatabaseMissing('categories', $cat->toArray());
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $post->id,
        'category_id' => $cat->id
    ]);
});
