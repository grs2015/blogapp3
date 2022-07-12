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
it('logged-in as an admin, renders single post entry by given slug', function() {
    $this->withoutExceptionHandling();

    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});
