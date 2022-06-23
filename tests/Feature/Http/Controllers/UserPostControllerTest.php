<?php

use App\Http\Controllers\UserPostController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

uses()->group('UserPostController');

/* ------------------------------ @index method (Admin part)----------------------------- */
it('renders the post page with posts data', function() {
    $user = User::factory()->hasPosts(3)->create(['id' => 1]);
    $posts = $user->posts;
    $postTitle = $posts[random_int(0, 2)]->title;

    $response = $this->get('/users/1/posts');

    $response->assertOk();
    $response->assertSee('All posts of User');
    $response->assertSee($user->first_name);
    $response->assertSee($postTitle);
});

it('renders the post page with all types of posts', function() {
    User::factory()->has(Post::factory()->count(4)->state(new Sequence(
        ['published' => Post::PUBLISHED],
        ['published' => Post::DRAFT],
        ['published' => Post::PENDING],
        ['published' => Post::UNPUBLISHED],
    )))->create();

    $response = $this->get('/users/1/posts');

    $response->assertOk();
    $response->assertSee(['published', 'draft', 'pending', 'unpublished']);
});

/* ------------------------------ @create method (Admin part)----------------------------- */
it('renders create post form', function() {
    $this->get('/posts/create')->assertSee('Form for post creation');
});

/* ------------------------------ @store method (Admin part)----------------------------- */
it('checks the validation and redirect', function() {
    $postData = [
        'title' => 'New Post',
        'meta_title' => 'Meta information',
        'summary' => 'Post summary information',
        'categories' => array(1, 2, 3)
    ];
    $user = User::factory()->create();

    $response = $this->post(action([UserPostController::class, 'store'],['user' => $user->id]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('users.posts.index', ['user' => $user->id]));
});

it('checks the session error when validation fails', function() {
    $postData = [
        'meta_title' => 'Meta information'
    ];
    $user = User::factory()->create();

    $response = $this->post(action([UserPostController::class, 'store'],['user' => $user->id]), $postData);

    $response->assertSessionHasErrors();
});

it('checks the stored post has some predefined properties', function() {
    $postData = [
        'title' => 'New Post',
        'meta_title' => 'Meta information',
        'summary' => 'Post summary information',
        'categories' => array(1, 2, 3)
    ];
    $user = User::factory()->create();

    $response = $this->post(action([UserPostController::class, 'store'],['user' => $user->id]), $postData);

    $this->assertDatabaseHas('posts', [
        'published' => Post::UNPUBLISHED,
        'favorite' => Post::NONFAVORITE,
        'views' => 0,
    ]);
});
