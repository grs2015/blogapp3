<?php

use App\Http\Controllers\TagController;
use App\Models\Tag;

uses()->group('TagController');

/* ------------------------------ @index method ----------------------------- */
it('renders the tag page with tags data', function() {
    $tags = Tag::factory()->count(5)->create();
    $tagTitle = Tag::inRandomOrder()->first()->title;

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All tags');
    $response->assertSee($tagTitle);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create tag form', function() {
    $this->get('/tags/create')->assertSee('Form for tag creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $tagData = [
        'title' => 'New Tag',
        'meta_title' => 'Meta information',
        'content' => 'Tag content',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('tags.index'));
});

it('checks the session error when validation fails', function() {
    $tagData = [
        'meta_title' => 'Meta information'
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertSessionHasErrors();
});
