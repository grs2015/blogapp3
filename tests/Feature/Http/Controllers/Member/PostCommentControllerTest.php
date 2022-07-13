<?php

use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Member\PostCommentController;

uses()->group('member');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsMember();
});

/* ----------------------------- @create method ----------------------------- */
it('renders create comment form', function() {
    $post = Post::factory()->create();
    $this->get(action([PostCommentController::class, 'create'], ['post' => $post->slug]))->assertSee('Form for comment creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $this->withoutExceptionHandling();
    $post = Post::factory()->create();
    $commentData = [
        'title' => 'New Comment',
        'content' => 'Content of comment',
        'published_at' => now()
    ];

    $response = $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('posts.show', ['post' => $post->slug]));
});

it('checks the session error when validation fails at storing', function() {
    $post = Post::factory()->create();
    $commentData = [
        'content' => 'Content of comment',
    ];

    $response = $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $response->assertSessionHasErrors();
});

it('checks the stored comment has some predefined properties and resides in database', function() {
    $post = Post::factory()->create();
    $time = now();
    $commentData = [
        'title' => 'New Comment',
        'content' => 'Content of comment',
        'published_at' => $time
    ];

    $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $this->assertDatabaseHas('comments', [
        'published' => Comment::UNPUBLISHED,
        'title' => 'New Comment',
        'published_at' => $time
    ]);
});
