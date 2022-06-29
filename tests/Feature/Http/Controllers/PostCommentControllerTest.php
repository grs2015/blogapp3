<?php

use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\PostCommentController;

uses()->group('PCC');

/* ------------------------------ @index method ----------------------------- */
it('renders the comment page with comments data', function() {
    $post = Post::factory()->hasComments(3)->create();
    $comments = $post->comments;
    $commentTitle = $comments[random_int(0, 2)]->title;

    $response = $this->get(action([PostCommentController::class, 'index'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertSee('All comments attached to Post');
    $response->assertSee($post->title);
    $response->assertSee($commentTitle);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create comment form', function() {
    $this->get('/comments/create')->assertSee('Form for comment creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $post = Post::factory()->create();
    $commentData = [
        'title' => 'New Comment',
        'content' => 'Content of comment',
        'published_at' => now()
    ];

    $response = $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('posts.comments.index', ['post' => $post->slug]));
});

it('checks the session error when validation fails', function() {
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
