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
