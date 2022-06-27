<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Postmeta;
use App\Events\PostCreated;
use App\Events\PostDeleted;

use App\Events\PostUpdated;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\UserPostController;
use App\Mail\PostCreatedNotificationMarkdown;
use App\Mail\PostUpdatedNotificationMarkdown;
use function Spatie\PestPluginTestTime\testTime;
use Illuminate\Database\Eloquent\Factories\Sequence;

uses()->group('UserPostController');
// beforeEach(function() {
//     $this->simpleUser = User::factory()->create();
//     $this->userWithPosts = User::factory()->hasPosts(3)->create();
//     $this->categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
//     $this->tagIds = Tag::factory()->count(3)->create()->pluck('id')->toArray();
// });
// TODO - As authorization will be implemented, add the policy tests for correspoding methods
/* ------------------------------ @index method (Admin part)----------------------------- */
it('renders the post page with posts data', function() {
    $user = User::factory()->hasPosts(3)->create();
    $posts = $user->posts;
    $postTitle = $posts[random_int(0, 2)]->title;

    $response = $this->get(action([UserPostController::class, 'index'], ['user' => $user->id]));

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
        'hero_image' => 'uploads/2022-01-01-00-00-00-test.jpg'
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
    $urlEntry = 'uploads/2022-01-01-00-00-00-test_1.jpg'.','.'uploads/2022-01-01-00-00-00-test_2.jpg';
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
        if ( ! $mail->hasTo(config('contacts.admin_email'))) {
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

/* ------------------------------- @show method (Admin part)------------------------------ */

it('renders single post entry by given slug', function() {
    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([UserPostController::class, 'show'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});

/* -------------------------------- @edit method (Admin part)------------------------------- */

it('renders edit form for single post entry by given slug', function() {
    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([UserPostController::class, 'show'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});

/* ----------------------------- @update method (Admin part)----------------------------- */

it('checks the validation and redirect at update', function() {
    // Arrange #1
    $user = User::factory()->create();
    $categoryIds = Category::factory()->create(['id' => 5])->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds,
    ];

    // Action #1
    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);
    // Assertion #1 - Check the correct data is saved to database, including pivot-tables
    $response->assertStatus(302);
    $post = Post::first();

    // Arrange #2 - Preparation data for update procedure
    $postData = [
        'title' => 'Updated post',
        'summary' => 'Summary of the updated post',
        'categories' => $categoryIds,
    ];
    // Action #2
    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2 - Check the data is updated in database, including pivot-tables
    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([UserPostController::class, 'edit'], ['user' => $user->id, 'post' => $post->slug]));
});

it('checks the stored post is in database as well as in pivot table at update', function() {
    // Arrange #1
    $user = User::factory()->create();
    $categoryIds = Category::factory()->create(['id' => 5])->pluck('id')->toArray();
    $tagIds = Tag::factory()->create(['id' => 7])->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds,
        'tags' => $tagIds
    ];
    // Action #1
    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);
    // Assertion #1 - Check the correct data is saved to database, including pivot-tables
    $response->assertStatus(302);
    $post = Post::first();
    $tagId = $post->tags->first()->id;
    $catId = $post->categories->first()->id;
    expect($tagId)->toBe(7);
    expect($catId)->toBe(5);
    $this->assertDatabaseHas('post_tag', [
        'tag_id' => $tagId,
        'post_id' => $post->id
    ]);
    $this->assertDatabaseHas('category_post', [
        'category_id' => $catId,
        'post_id' => $post->id
    ]);


    // Arrange #2 - Preparation data for update procedure
    Category::factory()->create(['id' => 6]);
    Tag::factory()->create(['id' => 8]);
    $postData = [
        'title' => 'Updated post',
        'summary' => 'Summary of the updated post',
        'categories' => array(6),
        'tags' => array(8)
    ];
    // Action #2
    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2 - Check the data is updated in database, including pivot-tables
    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('post_tag', [
        'tag_id' => 8,
        'post_id' => $post->id
    ]);
    $this->assertDatabaseHas('category_post', [
        'category_id' => 6,
        'post_id' => $post->id
    ]);
    $this->assertDatabaseHas('posts', ['title' => 'Updated post', 'summary' => 'Summary of the updated post']);
    $response->assertStatus(302);
    $response->assertRedirect(action([UserPostController::class, 'edit'], ['user' => $user->id, 'post' => $post->slug]));
});

it('checks the hero-image upload and substitutes the previous one in database after post updating', function() {
    // Arrange #1
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
    // Action #1
    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);
    // Assertion #1
    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test.jpg');
    $this->assertDatabaseHas('posts', [
        'hero_image' => 'uploads/2022-01-01-00-00-00-test.jpg'
    ]);
    $response->assertStatus(302);

    // Arrange #2
    testTime()->addHour();
    $file = UploadedFile::fake()->image('test.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'categories' => $categoryIds,
    ];
    $post = Post::first();
    // Action #2
    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2
    Storage::disk('public')->assertMissing('uploads/2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/2022-01-01-01-00-00-test.jpg');
    $this->assertDatabaseMissing('posts', [
        'hero_image' => 'uploads/2022-01-01-00-00-00-test.jpg'
    ]);
    $this->assertDatabaseHas('posts', [
        'hero_image' => 'uploads/2022-01-01-01-00-00-test.jpg'
    ]);
    $response->assertStatus(302);
});

it('checks the images upload and substitute the previous ones in database after post updating', function() {
    // Arrange #1
    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_1, $file_2),
        'categories' => $categoryIds,
    ];
    // Action #1
    $response = $this->post(action([UserPostController::class, 'store'], ['user' => $user->id]), $postData);
    // Assertion #1
    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/2022-01-01-00-00-00-test_2.jpg');
    $urlEntry = 'uploads/2022-01-01-00-00-00-test_1.jpg'.','.'uploads/2022-01-01-00-00-00-test_2.jpg';
    $this->assertDatabaseHas('posts', [
        'images' => $urlEntry
    ]);
    $response->assertStatus(302);

    // Arrange #2
    testTime()->addHour();
    $file_3 = UploadedFile::fake()->image('test_3.jpg');
    $file_4 = UploadedFile::fake()->image('test_4.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_3, $file_4),
        'categories' => $categoryIds,
    ];
    $post = Post::first();
    // Action #2
    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2
    $urlEntryNew = 'uploads/2022-01-01-01-00-00-test_3.jpg'.','.'uploads/2022-01-01-01-00-00-test_4.jpg';
    Storage::disk('public')->assertMissing('uploads/2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertMissing('uploads/2022-01-01-00-00-00-test_2.jpg');
    Storage::disk('public')->assertExists('uploads/2022-01-01-01-00-00-test_3.jpg');
    Storage::disk('public')->assertExists('uploads/2022-01-01-01-00-00-test_4.jpg');
    $this->assertDatabaseMissing('posts', [
        'images' => $urlEntry
    ]);
    $this->assertDatabaseHas('posts', [
        'images' => $urlEntryNew
    ]);
    $response->assertStatus(302);
});

it('checks the event firing after updating the post in database', function() {
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    Event::fake();

    $categoryIds = Category::pluck('id')->toArray();
    $postData = [
        'title' => 'Updated post',
        'categories' => $categoryIds
    ];

    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(PostUpdated::class);
});

it('checks the mail been sent to admin after updating the post in database', function() {
    Mail::fake();
    $user = User::factory()->author()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    $categoryIds = Category::pluck('id')->toArray();
    $postData = [
        'title' => 'Updated post',
        'categories' => $categoryIds
    ];

    $response = $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertSent(function(PostUpdatedNotificationMarkdown $mail) use ($postData, $user) {
        if ( ! $mail->hasTo(config('contacts.admin_email'))) {
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

it('checks if the slug attribute updated according updated title attribute', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    // Assertion #1
    expect($post->slug)->toBe('new-post-entry');
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    // Arrange #2
    $categoryIds = Category::pluck('id')->toArray();
    $postData = [
        'title' => 'Updated Post Entry',
        'categories' => $categoryIds
    ];
    // Act #2
    $this->put(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2
    $post->refresh();
    expect($post->slug)->toBe('updated-post-entry');
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
});

it('test the content of the PostUpdatedNotificationMarkdown mailable', function() {
    $user = User::factory()->create();

    $mailable = new PostUpdatedNotificationMarkdown($user, 'Post Title', 'Post Summary');

    $mailable->assertSeeInHtml($user->first_name);
    $mailable->assertSeeInHtml('Post Title');
    $mailable->assertSeeInHtml('Post Summary');
});

/* ----------------------------- @destroy method (Admin part)---------------------------- */

it('checks the deletion of entry as well as related models 1-M and entry in pivot-table', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(1))
        ->has(Tag::factory()->count(1))
        ->has(Comment::factory()->count(1))
        ->has(Postmeta::factory()->count(1))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    // Assertion #1
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    $this->assertDatabaseHas('comments', ['title' => Comment::first()->title]);
    $this->assertDatabaseHas('postmetas', ['key' => Postmeta::first()->key]);
    $this->assertDatabaseHas('category_post', ['category_id' => Category::first()->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('post_tag', ['tag_id' => Tag::first()->id, 'post_id' => $post->id]);

    $response = $this->delete(action([UserPostController::class, 'destroy'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertRedirect(route('users.posts.index', ['user' => $user->id]));
    $this->assertModelMissing($post);
    $this->assertDatabaseMissing('posts', $post->toArray());
    $this->assertDatabaseMissing('comments', Comment::first()->toArray());
    $this->assertDatabaseMissing('postmetas', Postmeta::first()->toArray());
    $this->assertDatabaseMissing('post_tag', [
        'post_id' => $post->id,
        'tag_id' => Tag::first()->id
    ]);
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $post->id,
        'category_id' => Category::first()->id
    ]);
});

it('checks the event firing after deletion the post in database', function() {
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    Event::fake();

    $response = $this->delete(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(PostDeleted::class);
});

it('checks the mail been sent to admin after deletion the post in database', function() {
    Mail::fake();
    $user = User::factory()->author()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($user)
        ->create(['title' => 'New Post Entry']);


    $response = $this->delete(action([UserPostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertSent(function(PostDeletedNotificationMarkdown $mail) use ($post, $user) {
        if ( ! $mail->hasTo(config('contacts.admin_email'))) {
            return false;
        }

        if ( $mail->title !== $post['title'] ) {
            return false;
        }

        if ( $mail->user->first_name !== $user->first_name) {
            return false;
        }
        return true;
    });
});

it('test the content of the PostDeletedNotificationMarkdown mailable', function() {

});
