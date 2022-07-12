<?php

use App\Models\Tag;

uses()->group('models');

it('test slug attribute when the tag is created', function() {
    $tag = Tag::factory()->create(['title' => 'New Tag Entry']);

    expect($tag->slug)->toEqual('new-tag-entry');
});
