<?php

use App\Models\Post;
use App\Http\Controllers\Member\PostRatingController;

uses()->group('ArticleRating');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
});

it('checks if post can be rated', function() {
    $this->withoutExceptionHandling();

    loginAsMember();

    $post = Post::factory()->create();

    $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]), ['rating' => 5]);

    expect($post->rating())->toEqual(5);
});

it('checks if post cannot be rated by guests', function() {
    // $this->withoutExceptionHandling();

    $post = Post::factory()->create();

    $response = $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]));

    $response->assertRedirect('login');

    expect($post->rating())->toBeEmpty();
});

it('checks the valid rating', function() {

    loginAsMember();

    $post = Post::factory()->create();

    $response = $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]));

    $response->assertSessionHasErrors('rating');

    $response = $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]), ['rating' => 'Bad']);

    $response->assertSessionHasErrors('rating');
});

it('checks if post can be updated with rating', function() {
    // $this->withoutExceptionHandling();

    loginAsMember();

    $post = Post::factory()->create();

    $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]), ['rating' => 5]);

    expect($post->rating())->toEqual(5);

    $this->post(action([PostRatingController::class, 'store'], ['post' => $post->slug]), ['rating' => 2]);

    $this->assertDatabaseHas('ratings', ['rating' => 2]);

    $post->refresh();

    expect($post->rating())->toEqual(2);
});
