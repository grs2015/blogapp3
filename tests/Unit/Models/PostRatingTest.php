<?php

use App\Models\Post;

use App\Models\User;
use App\Models\Rating;
use function Pest\Laravel\actingAs;

uses()->group('models');

beforeEach(function () {
    $this->post = Post::factory()->create();
    $this->user = User::factory()->create();
});

it('checks if the post can be rated', function() {

    $this->post->rate(5, $this->user);

    expect($this->post->ratings)->toHaveCount(1);
});

it('calculated the average rating', function() {
    $this->post->rate(5, $this->user);
    $this->post->rate(1, User::factory()->create());

    expect($this->post->rating())->toEqual(3);
});

it('checks that it cannot be rated above 5', function() {
    $this->post->rate(6);
})->throws(\InvalidArgumentException::class, 'Ratings must be between 1-5');

it('checks that it cannot be rated below 1', function() {
    $this->post->rate(-1);
})->throws(\InvalidArgumentException::class, 'Ratings must be between 1-5');

it('checks that can be rated once per user', function() {

    actingAs($this->user);


    $this->post->rate(5);
    $this->post->rate(3);

    expect($this->post->rating())->toEqual(3);
});
