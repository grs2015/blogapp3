<?php

use App\Models\Category;
use App\Http\Controllers\Author\CategoryController;

uses()->group('author');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAuthor();
});

/* ------------------------------ @index method ----------------------------- */
it('renders the category page with category data', function() {
    Category::factory()->count(5)->create();
    $catTitle = Category::inRandomOrder()->first()->title;

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All cats:');
    $response->assertSee($catTitle);

    loginAsAdmin();

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All cats:');
    $response->assertSee($catTitle);

    loginAsMember();

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertStatus(403);
});

/* ------------------------------ @show method ------------------------------ */
it('renders single cat entry by given slug', function() {
    $cat = Category::factory()->create();

    $response = $this->get(action([CategoryController::class, 'show'], ['category' => $cat->slug]));

    $response->assertSee($cat->title);
    $response->assertSee($cat->content);
    $response->assertSee($cat->slug);
    $response->assertDontSee($cat->summary);
});
