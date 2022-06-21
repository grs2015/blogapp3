<?php

use App\Models\Category;

uses()->group('CategoryModel');

it('test slug attribute when the category is created', function() {
    $category = Category::factory()->create(['title' => 'New Category Entry']);

    expect($category->slug)->toEqual('new-category-entry');
});
