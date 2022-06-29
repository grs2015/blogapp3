<?php

use App\Models\Category;
use App\Http\Controllers\CategoryController;

uses()->group('CategoryController');

/* ------------------------------ @index method ----------------------------- */
it('renders the cat page with cats data', function() {
    $tags = Category::factory()->count(5)->create();
    $catTitle = Category::inRandomOrder()->first()->title;

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All cats:');
    $response->assertSee($catTitle);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create cat form', function() {
    $this->get('/categories/create')->assertSee('Form for cat creations');
});
