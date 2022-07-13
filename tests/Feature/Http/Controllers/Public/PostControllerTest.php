<?php

use App\Models\Post;
use App\Models\User;
use App\Http\Controllers\Public\PostController;

uses()->group('public');

/* ------------------------------ @index method ----------------------------- */
it('renders the index view with posts/comments/postmeta data, users', function() {
    $this->withoutExceptionHandling();

    $posts = Post::factory(3)->hasComments(3)->hasPostmetas(3)->create();
    $commentTitle = $posts->first()->comments->first()->title;
    $metaKey = $posts->first()->postmetas->first()->key;
    $userFirstName = $posts->first()->user->first_name;


    $response = $this->get(action([PostController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All posts');
    $response->assertSee($commentTitle);
    $response->assertSee($metaKey);
    $response->assertSee($userFirstName);
});

/* ------------------------------ @show method ------------------------------ */
it('renders the public post page with post data by slug - version#1', function() {
    $this->withoutExceptionHandling();

    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
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

it('renders the public post page with post data by slug - version#2', function() {
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
