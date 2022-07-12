<?php

use App\Models\Category;
use App\Http\Controllers\Public\CategoryController;

uses()->group('public');

/* ------------------------------ @index method ----------------------------- */
it('renders the category page with category data', function() {
    Category::factory()->count(5)->create();
    $catTitle = Category::inRandomOrder()->first()->title;

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All cats:');
    $response->assertSee($catTitle);
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
