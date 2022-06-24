<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Events\PostCreated;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\UserPostController;
use App\Mail\PostCreatedNotificationMarkdown;

use function Spatie\PestPluginTestTime\testTime;
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
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'New Post',
        'meta_title' => 'Meta information',
        'summary' => 'Post summary information',
        'categories' => $categoryIds
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
        'title' => 'New Post'
    ]);
});

it('checks the stored post is in database as well as in pivot table', function() {
    $tagIds = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $user = User::factory()->create();
    $postData = [
        'title' => 'New Post Entry',
        'tags' => $tagIds,
        'categories' => $categoryIds
    ];

    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);
    $postId = Post::where('slug', 'new-post-entry')->first()->id;

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('posts', ['title' => 'New Post Entry']);
    $this->assertDatabaseHas('category_post', [
        'category_id' => $categoryIds[0],
        'category_id' => $categoryIds[1],
        'category_id' => $categoryIds[2],
        'post_id' => $postId
    ]);
    $this->assertDatabaseHas('post_tag', [
        'tag_id' => $tagIds[0],
        'tag_id' => $tagIds[1],
        'tag_id' => $tagIds[2],
        'post_id' => $postId
    ]);


});

it('checks the hero-image upload and its url resides in database after post storing', function() {
    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'categories' => $categoryIds,
    ];

    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);

    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test.jpg');
    $this->assertDatabaseHas('posts', [
        'hero_image' => '/storage/uploads/2022-01-01-00-00-00-test.jpg'
    ]);
    $response->assertStatus(302);
});

it('checks the post images upload and their urls are imploded in database after post storing', function() {
    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_1, $file_2),
        'categories' => $categoryIds
    ];

    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);

    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test_2.jpg');
    $urlEntry = '/storage/uploads/2022-01-01-00-00-00-test_1.jpg'.','.'/storage/uploads/2022-01-01-00-00-00-test_2.jpg';
    $this->assertDatabaseHas('posts', [
        'images' => $urlEntry
    ]);
    $response->assertStatus(302);
});

it('checks the event firing after storing the post in database', function() {
    Event::fake();
    $user = User::factory()->create();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds
    ];

    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(PostCreated::class);
});

it('checks the mail been sent after storing the post in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds
    ];

    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertSent(function(PostCreatedNotificationMarkdown $mail) use ($postData, $user) {
        if ( ! $mail->hasTo('admin@admin.com')) {
            return false;
        }

        if ( $mail->title !== $postData['title'] ) {
            return false;
        }

        if ( $mail->user->first_name !== $user->first_name) {
            return false;
        }
        return true;
    });
});

it('test the content of the PostCreatedNotificationMarkdown mailable', function() {
    $user = User::factory()->create();

    $mailable = new PostCreatedNotificationMarkdown($user, 'Post Title', 'Post Summary');

    $mailable->assertSeeInHtml($user->first_name);
    $mailable->assertSeeInHtml('Post Title');
    $mailable->assertSeeInHtml('Post Summary');
});

/* ------------------------------- Show method (Admin part)------------------------------ */

it('renders single post entry by given slug', function() {
    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([UserPostController::class, 'show'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});
