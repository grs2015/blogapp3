<?php

use App\Models\Post;
use App\Models\Postmeta;
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

/* ------------------------------ @show method ------------------------------ */
it('renders single postmeta entry by given ID', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();

    $response = $this->get(action([PostPostmetaController::class, 'show'], ['post' => $post->slug, 'postmeta' => $postmeta->id]));

    $response->assertSee($postmeta->key);
    $response->assertSee($post->title);
    $response->assertDontSee($post->content);
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for single postmeta entry by given ID', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();

    $response = $this->get(action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => $postmeta->id]));

    $response->assertSee($postmeta->key);
    $response->assertSee($postmeta->content);
    $response->assertDontSee($postmeta->summary);
    $response->assertSee($post->title);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the validation and redirect at update', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaData = [
        'key' => 'Updated comment',
        'content' => 'Updated comment content'
    ];

    $response = $this->put(action([PostPostmetaController::class, 'update'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]), $postmetaData);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]));
});

it('checks the updated postmeta is in database', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaData = [
        'key' => 'Updated postmeta Key',
        'content' => 'Updated postmeta content'
    ];

    $this->put(action([PostPostmetaController::class, 'update'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]), $postmetaData);

    $this->assertDatabaseHas('postmetas', ['key' => 'Updated postmeta Key']);
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();

    $response = $this->delete(action([PostPostmetaController::class, 'destroy'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]));

    $response->assertRedirect(route('posts.postmetas.index', ['post' => $post->slug]));
    $this->assertSoftDeleted($postmeta);
    // $this->assertModelMissing($postmeta);
    // $this->assertDatabaseMissing('postmetas', $postmeta->toArray());
});

