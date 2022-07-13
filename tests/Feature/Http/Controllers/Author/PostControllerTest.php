<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Postmeta;
use App\Events\PostCreated;
use App\Events\PostUpdated;
use Illuminate\Http\UploadedFile;
use App\Mail\PostCreatedNotificationMarkdown;
use App\Mail\PostUpdatedNotificationMarkdown;
use App\Http\Controllers\Author\PostController;
use function Spatie\PestPluginTestTime\testTime;

uses()->group('author');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    $this->user = loginAsAuthor();
});

/* ------------------------------ @index method ----------------------------- */
it('renders the index view with posts/comments/postmeta data, users', function() {
    $this->withoutExceptionHandling();

    $posts = Post::factory(3)->hasComments(3)->hasPostmetas(3)->for($this->user)->create();
    $commentTitle = $posts->first()->comments->first()->title;
    $metaKey = $posts->first()->postmetas->first()->key;
    $userFirstName = $posts->first()->user->first_name;

    $response = $this->get(action([PostController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All posts');
    $response->assertSee($commentTitle);
    $response->assertSee($metaKey);
    $response->assertSee($userFirstName);
});

/* ----------------------------- @create method ----------------------------- */
it('renders the form for post creation', function() {
    $this->withoutExceptionHandling();

    $response = $this->get(action([PostController::class, 'create']));

    $response->assertOk();
    $response->assertSee('Form for post creation');
});

/* ------------------------------ @store method----------------------------- */
it('checks the validation and redirect', function() {
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'New Post',
        'meta_title' => 'Meta information',
        'summary' => 'Post summary information',
        'categories' => $categoryIds
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('author.posts.index'));
});

it('checks the session error when validation fails', function() {
    $postData = [
        'meta_title' => 'Meta information'
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    $response->assertSessionHasErrors();
});

it('checks the stored post has some predefined properties', function() {
    $postData = [
        'title' => 'New Post',
        'meta_title' => 'Meta information',
        'summary' => 'Post summary information',
        'categories' => array(1, 2, 3)
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

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
    $postData = [
        'title' => 'New Post Entry',
        'tags' => $tagIds,
        'categories' => $categoryIds
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);
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

it('checks the hero-image and all thumbnails upload and its url resides in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'categories' => $categoryIds,
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/100-100-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/200-200-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertExists('uploads/640-480-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/100-100-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/200-200-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);
});

it('checks the post images upload and their urls are imploded in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_1, $file_2),
        'categories' => $categoryIds
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');

    expect(Post::first()->galleries()->first()->original)->toEqual('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    expect(Post::first()->galleries()->first()->thumbs)->toEqual('uploads/200-200-2022-01-01-00-00-00-test_1.jpg,uploads/640-480-2022-01-01-00-00-00-test_1.jpg');
    $urlEntry_1 = 'uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = 'uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = 'uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = 'uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = 'uploads/200-200-2022-01-01-00-00-00-test_2.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = 'uploads/200-200-2022-01-01-00-00-00-test_1.jpg'.
               ','.'uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_1,
        'lowres' => $urlEntry_3,
        'thumbs' => $urlEntry_6,
    ]);
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_2,
        'lowres' => $urlEntry_4,
        'thumbs' => $urlEntry_5,
    ]);
    $response->assertStatus(302);
});

it('checks the event firing after storing the post in database', function() {
    Event::fake();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(PostCreated::class);
});

it('checks the mail been sent after storing the post in database', function() {
    Mail::fake();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertSent(function(PostCreatedNotificationMarkdown $mail) use ($postData) {
        if ( ! $mail->hasTo(config('contacts.admin_email'))) {
            return false;
        }

        if ( $mail->title !== $postData['title'] ) {
            return false;
        }

        if ( $mail->user->first_name !== $this->user->first_name) {
            return false;
        }
        return true;
    });
});

it('test the content of the PostCreatedNotificationMarkdown mailable', function() {

    $mailable = new PostCreatedNotificationMarkdown($this->user, 'Post Title', 'Post Summary');

    $mailable->assertSeeInHtml($this->user->first_name);
    $mailable->assertSeeInHtml('Post Title');
    $mailable->assertSeeInHtml('Post Summary');
});

/* ------------------------------- @show method------------------------------ */
it('logged-in as author-owner of the post, renders single post entry by given slug', function() {
    $post = Post::factory()->for($this->user)->create();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($this->user->first_name);
});

it('logged-in as author-nonowner of the post, renders single post entry by given slug', function() {
    $user = User::factory()->create()->assignRole('author');
    $post = Post::factory()->for($user)->create();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertStatus(403);
});

it('logged-in as admin, renders single post entry by given slug', function() {
    loginAsAdmin();
    $post = Post::factory()->for($this->user)->create();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($this->user->first_name);
});

/* -------------------------------- @edit method------------------------------- */
it('renders edit form for single post entry by given slug', function() {
    $post = Post::factory()->for($this->user)->create();

    $response = $this->get(action([PostController::class, 'edit'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($this->user->first_name);
});

/* ----------------------------- @update method----------------------------- */
it('checks the validation and redirect at update', function() {
    // Arrange #1
    $categoryIds = Category::factory()->create(['id' => 5])->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds,
    ];

    // Action #1
    $response = $this->post(action([PostController::class, 'store']), $postData);
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
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
    // Assertion #2 - Check the data is updated in database, including pivot-tables
    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([PostController::class, 'edit'], ['post' => $post->slug]));
});

it('checks the stored post is in database as well as in pivot table at update', function() {
    // Arrange #1
    $categoryIds = Category::factory()->create(['id' => 5])->pluck('id')->toArray();
    $tagIds = Tag::factory()->create(['id' => 7])->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'categories' => $categoryIds,
        'tags' => $tagIds
    ];
    // Action #1
    $response = $this->post(action([PostController::class, 'store']), $postData);
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
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
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
    $response->assertRedirect(action([PostController::class, 'edit'], ['post' => $post->slug]));
});

it('checks the hero-image upload and substitutes the previous one in database after post updating', function() {
    // Arrange #1
    testTime()->freeze('2022-01-01 00:00:00');
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'categories' => $categoryIds,
    ];
    // Action #1
    $response = $this->post(action([PostController::class, 'store']), $postData);
    // Assertion #1
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/100-100-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/200-200-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
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
    // // Action #2
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
    // // Assertion #2
    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/100-100-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/200-200-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/640-480-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/100-100-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/200-200-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseMissing('posts', [
        'hero_image' => $urlEntry
    ]);
    $urlEntry = 'uploads/100-100-2022-01-01-01-00-00-test.jpg'.
                ','.'uploads/200-200-2022-01-01-01-00-00-test.jpg'.
                ','.'uploads/640-480-2022-01-01-01-00-00-test.jpg'.
                ','.'uploads/HiRes-2022-01-01-01-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-01-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
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
    $response = $this->post(action([PostController::class, 'store'], ['user' => $user->id]), $postData);
    // Assertion #1
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');
    $urlEntry_1 = 'uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = 'uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = 'uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = 'uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = 'uploads/200-200-2022-01-01-00-00-00-test_2.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = 'uploads/200-200-2022-01-01-00-00-00-test_1.jpg'.
               ','.'uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_1,
        'lowres' => $urlEntry_3,
        'thumbs' => $urlEntry_6,
    ]);
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_2,
        'lowres' => $urlEntry_4,
        'thumbs' => $urlEntry_5,
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
    $response = $this->put(action([PostController::class, 'update'], ['user' => $user->id, 'post' => $post->slug]), $postData);
    // Assertion #2

    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');

    $urlEntry_1 = 'uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = 'uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = 'uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = 'uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = 'uploads/200-200-2022-01-01-00-00-00-test_2.jpg'.
                ','.'uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = 'uploads/200-200-2022-01-01-00-00-00-test_1.jpg'.
               ','.'uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_1,
        'lowres' => $urlEntry_3,
        'thumbs' => $urlEntry_6,
    ]);
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_2,
        'lowres' => $urlEntry_4,
        'thumbs' => $urlEntry_5,
    ]);

    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-01-00-00-test_3.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-01-00-00-test_3.jpg');
    Storage::disk('public')->assertExists('uploads/HiRes-2022-01-01-01-00-00-test_4.jpg');
    Storage::disk('public')->assertExists('uploads/LoRes-2022-01-01-01-00-00-test_4.jpg');

    $urlEntry_1 = 'uploads/HiRes-2022-01-01-01-00-00-test_3.jpg';
    $urlEntry_2 = 'uploads/HiRes-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_3 = 'uploads/LoRes-2022-01-01-01-00-00-test_3.jpg';
    $urlEntry_4 = 'uploads/LoRes-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_5 = 'uploads/200-200-2022-01-01-01-00-00-test_4.jpg'.
                ','.'uploads/640-480-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_6 = 'uploads/200-200-2022-01-01-01-00-00-test_3.jpg'.
               ','.'uploads/640-480-2022-01-01-01-00-00-test_3.jpg';
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_1,
        'lowres' => $urlEntry_3,
        'thumbs' => $urlEntry_6,
    ]);
    $this->assertDatabaseHas('galleries', [
        'original' => $urlEntry_2,
        'lowres' => $urlEntry_4,
        'thumbs' => $urlEntry_5,
    ]);

    $response->assertStatus(302);
});

it('checks the event firing after updating the post in database', function() {
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($this->user)
        ->create(['title' => 'New Post Entry']);
    Event::fake();

    $categoryIds = Category::pluck('id')->toArray();
    $postData = [
        'title' => 'Updated post',
        'categories' => $categoryIds
    ];

    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(PostUpdated::class);
});

it('checks the mail been sent to admin after updating the post in database', function() {
    Mail::fake();
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($this->user)
        ->create(['title' => 'New Post Entry']);
    $categoryIds = Category::pluck('id')->toArray();
    $postData = [
        'title' => 'Updated post',
        'categories' => $categoryIds
    ];

    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertSent(function(PostUpdatedNotificationMarkdown $mail) use ($postData) {
        if ( ! $mail->hasTo(config('contacts.admin_email'))) {
            return false;
        }

        if ( $mail->title !== $postData['title'] ) {
            return false;
        }

        if ( $mail->user->first_name !== $this->user->first_name) {
            return false;
        }
        return true;
    });
});

it('checks if the slug attribute updated according updated title attribute', function() {
    // Arrange #1
    $post = Post::factory()
        ->has(Category::factory()->count(3))
        ->has(Tag::factory()->count(3))
        ->for($this->user)
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
    $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
    // Assertion #2
    $post->refresh();
    expect($post->slug)->toBe('updated-post-entry');
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
});

it('test the content of the PostUpdatedNotificationMarkdown mailable', function() {

    $mailable = new PostUpdatedNotificationMarkdown($this->user, 'Post Title', 'Post Summary');

    $mailable->assertSeeInHtml($this->user->first_name);
    $mailable->assertSeeInHtml('Post Title');
    $mailable->assertSeeInHtml('Post Summary');
});

/* ----------------------------- @destroy method DISABLED---------------------------- */
