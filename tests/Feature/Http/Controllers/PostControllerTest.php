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

/* ------------------------------ @show method ------------------------------ */
it('renders the public post page with post data by slug', function() {
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

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertSee('Post with all relationships:');
    $response->assertSee($postUser);
    $response->assertSee($postTitle);
    $response->assertSee($postCommentTitle);
    $response->assertSee($postTagTitle);
    $response->assertSee($postCategoryTitle);
    $response->assertSee($postPostmetaKey);
});

it('increases the post view count for new visitor', function() {
    $post = Post::factory()
        ->hasCategories(3)
        ->create();
    // Action #1
    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));
    // Asserion #1 - views counter increased by 1
    $response->assertOk();
    $post->refresh();
    $response->assertSeeText("Post views: {$post->views}");
    expect($post->views)->toEqual(1);
    // Action #2
    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));
    // Assertion #2 - views counter doesn't increase - the session is not invalidated
    $post->refresh();
    $response->assertSessionHas('viewed_posts', array($post->id));
    expect($post->views)->toEqual(1);

    // Arrange #3 - session invalidation
    session()->invalidate();
    // Action #3
    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));
    // Assertion #3 - views counter increases once again, after session invalidation
    $post->refresh();
    expect($post->views)->toEqual(2);
});
