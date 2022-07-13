<?php

use App\Models\Tag;
use App\Http\Controllers\Author\TagController;

uses()->group('author');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAuthor();
});

/* ------------------------------ @index method ----------------------------- */
it('renders the tag page with tags data', function() {
    // $this->withoutExceptionHandling();
    Tag::factory()->count(5)->create();
    $tagTitle = Tag::inRandomOrder()->first()->title;

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All tags');
    $response->assertSee($tagTitle);

    loginAsAdmin();

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All tags');
    $response->assertSee($tagTitle);

    loginAsMember();

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertStatus(403);
});

/* ------------------------------ @show method ------------------------------ */
it('renders single tag entry by given slug', function() {
    $tag = Tag::factory()->create();

    $response = $this->get(action([TagController::class, 'show'], ['tag' => $tag->slug]));

    $response->assertSee($tag->title);
    $response->assertSee($tag->content);
    $response->assertSee($tag->slug);
    $response->assertDontSee($tag->summary);
});
