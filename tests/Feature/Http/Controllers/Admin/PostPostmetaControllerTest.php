<?php

use App\Models\Post;
use App\Models\Postmeta;
use App\Http\Controllers\Admin\PostPostmetaController;
use Inertia\Testing\AssertableInertia as Assert;



uses()->group('admin', 'postmeta');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the postmeta page with Inertia', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaKey = $post->postmetas[0]->key;

    $response = $this->get(action([PostPostmetaController::class, 'index'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Postmeta/Index')
        ->has('model', fn(Assert $page) => $page
            ->has('postmetas', 3)
            ->has('postmetas.0', fn(Assert $page) => $page
                ->where('key', $postmetaKey)
                ->etc()
            )
        )
    );
});

/* ----------------------------- @create method ----------------------------- */
it('renders create postmeta form with Inertia', function() {
    $post = Post::factory()->create();
    $response = $this->get(action([PostPostmetaController::class, 'create'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Postmeta/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('postmeta')
            ->missing('postmeta.address')
            ->etc()
            )
        );
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for single postmeta with Inertia', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();
    $postmetaKey = $postmeta->key;

    $response = $this->get(action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => $postmeta->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Postmeta/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('postmeta', fn(Assert $page) => $page
                ->where('key', $postmetaKey)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @show method ------------------------------ */
it('renders single postmeta entry with Inertia', function() {
    $this->withoutExceptionHandling();

    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();
    $postmetaKey = $postmeta->key;

    $response = $this->get(action([PostPostmetaController::class, 'show'], ['post' => $post->slug, 'postmeta' => $postmeta->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Postmeta/Show')
        ->has('model', fn(Assert $page) => $page
            ->has('postmeta', fn(Assert $page) => $page
                ->where('key', $postmetaKey)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @store method ----------------------------- */
it('checks the postmeta data validation and redirect', function() {
    $this->withoutExceptionHandling();
    $post = Post::factory()->create();
    $postmetaData = [
        'key' => 'New Postmeta Key',
        'content' => 'Content of postmeta',
        'post_id' => $post->id
    ];

    $response = $this->post(action([PostPostmetaController::class, 'store'],['post' => $post->slug]), $postmetaData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.posts.postmetas.index', ['post' => $post->slug]));
});

it('checks the session error when validation of postmeta fails at storing', function() {
    $post = Post::factory()->create();
    $postmetaData = [
        'content' => 'Content of comment',
    ];

    $response = $this->post(action([PostPostmetaController::class, 'store'],['post' => $post->slug]), $postmetaData);

    $response->assertSessionHasErrors();
});

it('checks the stored postmeta data has some predefined properties and resides in database', function() {
    $post = Post::factory()->create();
    $postmetaData = [
        'key' => 'New Postmeta Key',
        'content' => 'Content of postmeta',
        'post_id' => $post->id
    ];

    $response = $this->post(action([PostPostmetaController::class, 'store'],['post' => $post->slug]), $postmetaData);

    $this->assertDatabaseHas('postmetas', [
        'key' => 'New Postmeta Key',
        'content' => 'Content of postmeta',
    ]);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the postmeta data for validation and redirect at update', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaData = [
        'key' => 'Updated comment',
        'content' => 'Updated comment content',
        'post_id' => $post->id,
        'id' => Postmeta::first()->id
    ];

    $response = $this->put(action([PostPostmetaController::class, 'update'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]), $postmetaData);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([PostPostmetaController::class, 'edit'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]));
});

it('checks the updated postmeta is in database', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaData = [
        'key' => 'Updated Key',
        'content' => 'Updated content',
        'post_id' => $post->id,
        'id' => Postmeta::first()->id
    ];

    $this->put(action([PostPostmetaController::class, 'update'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]), $postmetaData);

    $this->assertDatabaseHas('postmetas', ['key' => 'Updated Key']);
});

it('checks the session error when validation of postmeta fails at update', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmetaData = [
        'content' => 'Updated comment content',
    ];

    $response = $this->put(action([PostPostmetaController::class, 'update'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]), $postmetaData);

    $response->assertSessionHasErrors();
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of postmeta entry', function() {
    $post = Post::factory()->hasPostmetas(3)->create();
    $postmeta = Postmeta::first();

    $response = $this->delete(action([PostPostmetaController::class, 'destroy'], ['post' => $post->slug, 'postmeta' => Postmeta::first()->id]));

    $response->assertRedirect(route('admin.posts.postmetas.index', ['post' => $post->slug]));
    $this->assertModelMissing($postmeta);
    $this->assertDatabaseMissing('postmetas', $postmeta->toArray());
});

