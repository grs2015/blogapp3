<?php

use App\Enums\PostStatus;
use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Postmeta;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Admin\PostController;
use function Spatie\PestPluginTestTime\testTime;

uses()->group('admin', 'post');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the posts page with tags/categories/user data with Inertia', function() {
    $this->withoutExceptionHandling();

    $posts = Post::factory(3)
        ->hasComments(3)
        ->hasPostmetas(3)
        ->hasTags(3)
        ->hasCategories(3)
        ->create();

    $tagContent = Tag::first()->content;
    $catContent = Category::first()->content;
    $userEmail = User::find(2)->email;

    $response = $this->get(action([PostController::class, 'index']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Post/Index')
        ->has('model', fn(Assert $page) => $page
            ->has('posts', 13)
            ->has('posts', fn(Assert $page) => $page
              ->has('data', 3)
              ->has('data.0', fn(Assert $page) => $page
                ->has('user')
                ->has('user', fn(Assert $page) => $page
                    ->where('email', $userEmail)
                    ->etc())
                ->etc())
              ->etc())
            ->etc()
            )
    );
});
/* ----------------------------- @create method ----------------------------- */
it('renders create post form with Inertia', function() {
    $response = $this->get(action([PostController::class, 'create']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Post/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('post')
            ->missing('post.title')
            ->etc()
            )
        );
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit post form with Inertia', function() {
    $this->withoutExceptionHandling();

    $post = Post::factory()
        ->hasComments(3)
        ->hasPostmetas(3)
        ->hasTags(3)
        ->hasCategories(3)
        ->create();

    $tagContent = Tag::first()->content;
    $catContent = Category::first()->content;
    $userEmail = User::find(2)->email;
    $postSlug = $post->slug;

    $response = $this->get(action([PostController::class, 'edit'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Post/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('categories')
            ->has('tags')
            ->has('post')
            ->has('post', fn(Assert $page) => $page
                ->has('tags', 3)
                ->has('categories', 3)
                ->has('user')
                ->where('slug',$postSlug)
                ->etc()
                ->has('tags.0', fn(Assert $page) => $page
                    ->where('content', $tagContent)
                    ->etc())
                ->has('categories.0', fn(Assert $page) => $page
                    ->where('content', $catContent)
                    ->etc())
                ->has('user', fn(Assert $page) => $page
                    ->where('email', $userEmail)
                    ->etc())
            )
        )
    );
});

/* ------------------------------ @show method ------------------------------ */
it('renders single post page with Inertia', function() {
    $this->withoutExceptionHandling();

    $post = Post::factory()
        ->hasComments(3)
        ->hasPostmetas(3)
        ->hasTags(3)
        ->hasCategories(3)
        ->create();

    $tagContent = Tag::first()->content;
    $catContent = Category::first()->content;
    $userEmail = User::find(2)->email;
    $postSlug = $post->slug;

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Post/Show')
        ->has('model', fn(Assert $page) => $page
            ->missing('categories')
            ->missing('tags')
            ->has('post')
            ->has('post', fn(Assert $page) => $page
                ->has('tags', 3)
                ->has('categories', 3)
                ->has('user')
                ->where('slug',$postSlug)
                ->etc()
                ->has('tags.0', fn(Assert $page) => $page
                    ->where('content', $tagContent)
                    ->etc())
                ->has('categories.0', fn(Assert $page) => $page
                    ->where('content', $catContent)
                    ->etc())
                ->has('user', fn(Assert $page) => $page
                    ->where('email', $userEmail)
                    ->etc())
            )
        )
    );
});

/* ------------------------------ @store method ----------------------------- */
it('logged-in as an admin, checks the stored post is in database as well as in pivot table', function() {
    $this->withoutExceptionHandling();

    $tag_ids = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $cat_ids = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $user = User::first();
    $postData = [
        'title' => 'New Post Entry',
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'hero_image' => $file,
        'images' => array($file_1, $file_2),
        'author_id' => $user->id,
        'status' => 'pending',
        'favorite' => 'favorite'
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);
    $postId = Post::where('slug', 'new-post-entry')->first()->id;

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $this->assertDatabaseHas('posts', ['title' => 'New Post Entry']);
    $this->assertDatabaseHas('category_post', [
        'category_id' => $cat_ids[0],
        'category_id' => $cat_ids[1],
        'category_id' => $cat_ids[2],
        'post_id' => $postId
    ]);
    $this->assertDatabaseHas('post_tag', [
        'tag_id' => $tag_ids[0],
        'tag_id' => $tag_ids[1],
        'tag_id' => $tag_ids[2],
        'post_id' => $postId
    ]);
});

it('logged-in as an admin, checks the hero-image and all its thumbnails upload and url resides in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $user->assignRole('admin');
    $tag_ids = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $cat_ids = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'images' => array($file_1, $file_2),
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    // Storage::disk('public')->assertExists('uploads/100-100-2022-01-01-00-00-00-test.jpg');
    // Storage::disk('public')->assertExists('uploads/200-200-2022-01-01-00-00-00-test.jpg');
    Storage::assertExists('uploads/640-480-2022-01-01-00-00-00-test.jpg');
    $urlEntry = '/storage/uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);
});

it('logged-in as an admin, checks the post images upload and their urls are imploded in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $user->assignRole('admin');
    $tag_ids = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $cat_ids = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_1, $file_2),
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
    ];

    $response = $this->post(action([PostController::class, 'store']), $postData);

    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');

    expect(Post::first()->galleries()->first()->original)->toEqual('/storage/uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    expect(Post::first()->galleries()->first()->thumbs)->toEqual('/storage/uploads/640-480-2022-01-01-00-00-00-test_1.jpg');
    $urlEntry_1 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
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


/* ----------------------------- @update method ----------------------------- */
it('logged-in as an admin, checks the updated post is in database as well as in pivot table at update', function() {
    // Arrange #1
    $user = User::factory()->create();
    $cat_ids = Category::factory()->create(['id' => 5])->pluck('id')->toArray();
    $tag_ids = Tag::factory()->create(['id' => 7])->pluck('id')->toArray();
    $postData = [
        'title' => 'Newest post',
        'summary' => 'Summary of the newest post',
        'cat_ids' => $cat_ids,
        'tag_ids' => $tag_ids,
        'author_id' => $user->id,
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
    $cat_ids = Category::factory()->create(['id' => 6])->pluck('id')->toArray();
    $tag_ids = Tag::factory()->create(['id' => 8])->pluck('id')->toArray();
    $postData = [
        'title' => 'Updated post',
        'summary' => 'Summary of the updated post',
        'cat_ids' => $cat_ids,
        'tag_ids' => $tag_ids,
        'author_id' => $user->id,
        'id' => $post->id
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
    $post->refresh();
    $response->assertRedirect(action([PostController::class, 'edit'], ['post' => $post->slug]));
});

it('logged-in as an admin, checks the hero-image upload and substitutes the previous one in database after post updating', function() {
    // Arrange #1
    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $tag_ids = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $cat_ids = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
    ];
    // Action #1
    $response = $this->post(action([PostController::class, 'store']), $postData);
    // Assertion #1
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    $urlEntry = '/storage/uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);

    // Arrange #2
    testTime()->addHour();
    $file = UploadedFile::fake()->image('test.jpg');
    $post = Post::first();
    $postData = [
        'title' => 'Newest post',
        'hero_image' => $file,
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
        'id' => $post->id
    ];
    // Action #2
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
    // Assertion #2
    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/640-480-2022-01-01-00-00-00-test.jpg');
    $urlEntry = 'uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseMissing('posts', [
        'hero_image' => $urlEntry
    ]);
    $urlEntry = '/storage/uploads/640-480-2022-01-01-01-00-00-test.jpg'.
                ','.'/storage/uploads/HiRes-2022-01-01-01-00-00-test.jpg'.
                ','.'/storage/uploads/LoRes-2022-01-01-01-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);
});

it('logged-in as an admin, checks the images upload and substitute the previous ones in database after post updating', function() {
    // Arrange #1
    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $tag_ids = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $cat_ids = Category::factory()->count(3)->create()->pluck('id')->toArray();
    Storage::fake('public');
    $file_1 = UploadedFile::fake()->image('test_1.jpg');
    $file_2 = UploadedFile::fake()->image('test_2.jpg');
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_1, $file_2),
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
    ];
    // Action #1
    $response = $this->post(action([PostController::class, 'store']), $postData);
    // Assertion #1
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');
    $urlEntry_1 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
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
    $post = Post::first();
    $postData = [
        'title' => 'Newest post',
        'images' => array($file_3, $file_4),
        'tag_ids' => $tag_ids,
        'cat_ids' => $cat_ids,
        'author_id' => $user->id,
        'id' => $post->id
    ];
    // Action #2
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
    // Assertion #2

    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');

    $urlEntry_1 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_2 = '/storage/uploads/HiRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_3 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_1.jpg';
    $urlEntry_4 = '/storage/uploads/LoRes-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_5 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_2.jpg';
    $urlEntry_6 = '/storage/uploads/640-480-2022-01-01-00-00-00-test_1.jpg';
    $this->assertDatabaseMissing('galleries', [
        'original' => $urlEntry_1,
        'lowres' => $urlEntry_3,
        'thumbs' => $urlEntry_6,
    ]);
    $this->assertDatabaseMissing('galleries', [
        'original' => $urlEntry_2,
        'lowres' => $urlEntry_4,
        'thumbs' => $urlEntry_5,
    ]);

    Storage::assertExists('uploads/HiRes-2022-01-01-01-00-00-test_3.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-01-00-00-test_3.jpg');
    Storage::assertExists('uploads/HiRes-2022-01-01-01-00-00-test_4.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-01-00-00-test_4.jpg');

    $urlEntry_1 = '/storage/uploads/HiRes-2022-01-01-01-00-00-test_3.jpg';
    $urlEntry_2 = '/storage/uploads/HiRes-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_3 = '/storage/uploads/LoRes-2022-01-01-01-00-00-test_3.jpg';
    $urlEntry_4 = '/storage/uploads/LoRes-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_5 = '/storage/uploads/640-480-2022-01-01-01-00-00-test_4.jpg';
    $urlEntry_6 = '/storage/uploads/640-480-2022-01-01-01-00-00-test_3.jpg';
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


/* ----------------------------- @destroy method ---------------------------- */
it('logged-in as an admin, checks the deletion of entry', function() {
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

    $response = $this->delete(action([PostController::class, 'destroy'], ['post' => $post->slug]));

    $response->assertRedirect(route('admin.posts.index'));
    $this->assertModelExists($post);
    $this->assertDatabaseMissing('posts', $post->toArray());
    $this->assertDatabaseMissing('comments', Comment::first()->toArray());
    $this->assertDatabaseMissing('postmetas', Postmeta::first()->toArray());
});

/* ----------------------------- @delete method ----------------------------- */
it('logged-in as an admin, forces to delete the trashed entries given by array of IDs as well as related models 1-M and entry in pivot-table', function() {
    $this->withoutExceptionHandling();

    Storage::fake('public');
    $file = UploadedFile::fake()->image('test.jpg');
    $user = User::factory()->create();
    $posts = Post::factory()->
        for($user)->
        has(Tag::factory()->count(1))->
        has(Category::factory()->count(1))->
        count(2)->
        create(['hero_image' => $file]);
    $postIds = $posts->pluck('id')->toArray();

    $this->delete(action([PostController::class, 'destroy'], ['post' => $posts[0]->slug]));
    $this->delete(action([PostController::class, 'destroy'], ['post' => $posts[1]->slug]));

    $deletedAt = Post::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($posts[0]);
    $this->assertSoftDeleted($posts[1]);
    $this->assertDatabaseHas('posts', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([PostController::class, 'delete']), ['ids' => $postIds]);

    $this->assertDataBaseMissing('posts', ['id' => $posts[0]->id, 'id' => $posts[1]->id]);
    // $this->assertModelMissing($this->posts[0]);
    // $this->assertModelMissing($this->posts[1]);
    // $this->assertDatabaseMissing('posts', ['deleted_at' => $deletedAt]);

    // $this->assertDatabaseMissing('post_tag', [
    //     'post_id' => $this->posts[0]->id,
    //     'post_id' => $this->posts[1]->id,
    //     'tag_id' => Tag::first()->id
    // ]);
    // $this->assertDatabaseMissing('category_post', [
    //     'post_id' => $this->posts[0]->id,
    //     'post_id' => $this->posts[1]->id,
    //     'category_id' => Category::first()->id
    // ]);

    // $response->assertRedirect(route('admin.posts.index'));
});

/* ----------------------------- @restore method ---------------------------- */
it('logged-in as an admin, restores the trashed entries given by array of IDs', function() {
    $this->user = User::factory()->create();
    $this->posts = Post::factory()->
        for($this->user)->
         has(Tag::factory()->count(1))->
         has(Category::factory()->count(1))->
         count(2)->
         create();
    $this->postIds = $this->posts->pluck('id')->toArray();

    $this->delete(action([PostController::class, 'destroy'], ['post' => $this->posts[0]->slug]));
    $this->delete(action([PostController::class, 'destroy'], ['post' => $this->posts[1]->slug]));

    $deletedAt = Post::withTrashed()->get()->first()->deleted_at;
    $this->assertSoftDeleted($this->posts[0]);
    $this->assertSoftDeleted($this->posts[1]);
    $this->assertDatabaseHas('posts', ['deleted_at' => $deletedAt]);

    $response = $this->post(action([PostController::class, 'restore']), ['ids' => $this->postIds]);

    $this->assertDataBaseHas('posts', ['id' => $this->posts[0]->id, 'id' => $this->posts[1]->id]);
    $this->assertModelExists($this->posts[0]);
    $this->assertModelExists($this->posts[1]);
    $this->assertDatabaseMissing('posts', ['deleted_at' => $deletedAt]);

    $response->assertRedirect(route('admin.posts.index'));
 });
