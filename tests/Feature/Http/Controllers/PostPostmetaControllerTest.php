<?php

use App\Models\Post;
use App\Http\Controllers\PostPostmetaController;

uses()->group('PPMC');

/* ------------------------------ @index method ----------------------------- */
it('renders the postmeta page with postmeta data', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetas = $post->postmetas;
    $postmetaKey = $postmetas[random_int(0, 2)]->key;

    $response = $this->get(action([PostPostmetaController::class, 'index'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertSee('All postmetas attached to Post');
    $response->assertSee($post->title);
    $response->assertSee($postmetaKey);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create postmeta form', function() {
    $this->get('/postmetas/create')->assertSee('Form for postmeta creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $post = Post::factory()->create();
    $postmetaData = [
        'key' => 'New Meta',
        'content' => 'Content of Metadata',
    ];

    $response = $this->post(action([PostPostmetaController::class, 'store'],['post' => $post->slug]), $postmetaData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('posts.postmetas.index', ['post' => $post->slug]));
});
