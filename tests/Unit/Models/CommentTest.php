<?php

use App\Models\Comment;

uses()->group('CommentModel');

it('check if published comment is published', function() {
    $publishedComment = Comment::factory()->published()->create();
    expect($publishedComment->isPublished())->toBeTrue();

    $pendingComment = Comment::factory()->pending()->create();
    expect($pendingComment->isPublished())->toBeFalse();
});

it('check if pending comment is pending', function() {
    $pendingComment = Comment::factory()->pending()->create();
    expect($pendingComment->isPending())->toBeTrue();

    $publishedComment = Comment::factory()->published()->create();
    expect($publishedComment->isPending())->toBeFalse();
});

it('check if unpublished comment is unpublished', function() {
    $unpublishedComment = Comment::factory()->unpublished()->create();
    expect($unpublishedComment->isUnpublished())->toBeTrue();

    $publishedComment = Comment::factory()->published()->create();
    expect($publishedComment->isUnpublished())->toBeFalse();
});

it('has a scope to retrieve all published comments', function() {
    $publishedComment = Comment::factory()->published()->create();

    $publishedComments = Comment::wherePublished()->get();

    expect($publishedComments)->toHaveCount(1);
    expect($publishedComments[0]->id)->toEqual($publishedComment->id);
});

it('has a scope to retrieve all pending comments', function() {
    $pendingComment = Comment::factory()->pending()->create();

    $pendingComments = Comment::wherePending()->get();

    expect($pendingComments)->toHaveCount(1);
    expect($pendingComments[0]->id)->toEqual($pendingComment->id);
});

it('has a scope to retrieve all unpublished comments', function() {
    $unpublishedComment = Comment::factory()->unpublished()->create();

    $unpublishedComments = Comment::whereUnpublished()->get();

    expect($unpublishedComments)->toHaveCount(1);
    expect($unpublishedComments[0]->id)->toEqual($unpublishedComment->id);
});

