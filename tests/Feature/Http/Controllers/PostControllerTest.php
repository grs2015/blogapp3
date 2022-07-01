<?php

use App\Models\Post;
use App\Http\Controllers\PostController;

uses()->group('PostController');

/* ------------------------------ @index method ----------------------------- */
it('renders the public post page with posts data', function() {
    $posts = Post::factory()
        ->count(3)
        ->hasComments(3)
        ->hasCategories(3)
        ->hasTags(3)
        ->hasPostmetas(3)
        ->create();
    $post = $posts[random_int(0, 2)];
    $postTitle = $post->title;
    $postUser = $post->user->first_name;
    $postCommentTitle = $post->comments->first()->title;
    $postTagTitle=  $post->tags->first()->title;
    $postCategoryTitle=  $post->categories->first()->title;
    $postPostmetaKey=  $post->postmetas->first()->key;

    $response = $this->get(action([PostController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All posts with all relationships');
    $response->assertSee($postUser);
    $response->assertSee($postTitle);
    $response->assertSee($postCommentTitle);
    $response->assertSee($postTagTitle);
    $response->assertSee($postCategoryTitle);
    $response->assertSee($postPostmetaKey);

});
