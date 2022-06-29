<?php

use App\Models\Post;
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
