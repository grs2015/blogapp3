<?php

use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\actingAs;

uses()->group('ArticleRating');

it('checks if post can be rated', function() {
    // $this->withoutExceptionHandling();

    actingAs(User::factory()->create());

    $post = Post::factory()->create();

    $this->post("/posts/{$post->slug}/rate", ['rating' => 5]);

    expect($post->rating())->toEqual(5);
});

it('checks if post cannot be rated by guests', function() {
    $this->withoutExceptionHandling();

    $post = Post::factory()->create();

    $response = $this->post("/posts/{$post->slug}/rate");

    $response->assertRedirect('login');

    expect($post->rating())->toEqual(5);
});
