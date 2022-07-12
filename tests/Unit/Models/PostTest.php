<?php

use App\Models\Post;

uses()->group('models');

it('test slug attribute when the post is created', function() {
    $post = Post::factory()->create(['title' => 'New Post Entry']);

    expect($post->slug)->toEqual('new-post-entry');
});

it('check if published post is published', function() {
    $publishedPost = Post::factory()->published()->create();
    expect($publishedPost->isPublished())->toBeTrue();

    $draftPost = Post::factory()->draft()->create();
    expect($draftPost->isPublished())->toBeFalse();
});

it('check if drafted post is draft', function() {
    $draftPost = Post::factory()->draft()->create();
    expect($draftPost->isDraft())->toBeTrue();

    $publishedPost = Post::factory()->published()->create();
    expect($publishedPost->isDraft())->toBeFalse();
});

it('check if pending post is pended', function() {
    $draftPost = Post::factory()->pending()->create();
    expect($draftPost->isPending())->toBeTrue();

    $publishedPost = Post::factory()->published()->create();
    expect($publishedPost->isPending())->toBeFalse();
});

it('check if unpublished post is unpublished', function() {
    $draftPost = Post::factory()->unpublished()->create();
    expect($draftPost->isUnpublished())->toBeTrue();

    $publishedPost = Post::factory()->published()->create();
    expect($publishedPost->isUnpublished())->toBeFalse();
});

it('has a scope to retrieve all published blogposts', function() {
    $publishedPost = Post::factory()->published()->create();

    $publishedPosts = Post::wherePublished()->get();

    expect($publishedPosts)->toHaveCount(1);
    expect($publishedPosts[0]->id)->toEqual($publishedPost->id);
});

it('has a scope to retrieve all drafted blogposts', function() {
    $draftPost = Post::factory()->draft()->create();

    $draftPosts = Post::whereDraft()->get();

    expect($draftPosts)->toHaveCount(1);
    expect($draftPosts[0]->id)->toEqual($draftPost->id);
});

it('has a scope to retrieve all pending blogposts', function() {
    $pendingPost = Post::factory()->pending()->create();

    $pendingPosts = Post::wherePending()->get();

    expect($pendingPosts)->toHaveCount(1);
    expect($pendingPosts[0]->id)->toEqual($pendingPost->id);
});

it('has a scope to retrieve all unpublished blogposts', function() {
    $unpublishedPost = Post::factory()->unpublished()->create();

    $unpublishedPosts = Post::whereUnpublished()->get();

    expect($unpublishedPosts)->toHaveCount(1);
    expect($unpublishedPosts[0]->id)->toEqual($unpublishedPost->id);
});
