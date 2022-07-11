<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Postmeta;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Admin\PostController;
use function Spatie\PestPluginTestTime\testTime;

uses()->group('admin');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the index view with posts/comments/postmeta data, users', function() {
    $this->withoutExceptionHandling();

    $posts = Post::factory(3)->hasComments(3)->hasPostmetas(3)->create();
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
/* ------------------------------ @store method ----------------------------- */
it('logged-in as an admin, checks the stored post is in database as well as in pivot table', function() {
    $tagIds = Tag::factory()->count(3)->create()->pluck('id')->toArray();
    $categoryIds = Category::factory()->count(3)->create()->pluck('id')->toArray();
    $user = User::factory()->create();
    $user->assignRole('admin');
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

it('logged-in as an admin, checks the hero-image and all thumbnails upload and its url resides in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create();
    $user->assignRole('admin');
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

it('logged-in as an admin, checks the post images upload and their urls are imploded in database after post storing', function() {
    $this->withoutExceptionHandling();

    testTime()->freeze('2022-01-01 00:00:00');
    $user = User::factory()->create()->assignRole('admin');
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

/* ------------------------------ @show method ------------------------------ */
it('logged-in as an admin, renders single post entry by given slug', function() {
    $this->withoutExceptionHandling();

    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([PostController::class, 'show'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});

/* ------------------------------ @edit method ------------------------------ */
it('logged-in as an admin, renders edit form for single post entry by given slug', function() {
    $this->withoutExceptionHandling();

    $user = User::factory()->hasPosts(3)->create();
    $post = Post::first();

    $response = $this->get(action([PostController::class, 'edit'], ['post' => $post->slug]));

    $response->assertSee($post->title);
    $response->assertSee($post->slug);
    $response->assertDontSee($post->summary);
    $response->assertSee($user->first_name);
});

/* ----------------------------- @update method ----------------------------- */
it('logged-in as an admin, checks the stored post is in database as well as in pivot table at update', function() {
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

it('logged-in as an admin, checks the hero-image upload and substitutes the previous one in database after post updating', function() {
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

it('logged-in as an admin, checks the images upload and substitute the previous ones in database after post updating', function() {
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
    $response = $this->post(action([PostController::class, 'store']), $postData);
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
    $response = $this->put(action([PostController::class, 'update'], ['post' => $post->slug]), $postData);
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

    $response = $this->post(action([PostController::class, 'delete']), ['ids' => $this->postIds]);

    $this->assertDataBaseMissing('posts', ['id' => $this->posts[0]->id, 'id' => $this->posts[1]->id]);
    $this->assertModelMissing($this->posts[0]);
    $this->assertModelMissing($this->posts[1]);
    $this->assertDatabaseMissing('posts', ['deleted_at' => $deletedAt]);

    $this->assertDatabaseMissing('post_tag', [
        'post_id' => $this->posts[0]->id,
        'post_id' => $this->posts[1]->id,
        'tag_id' => Tag::first()->id
    ]);
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $this->posts[0]->id,
        'post_id' => $this->posts[1]->id,
        'category_id' => Category::first()->id
    ]);

    $response->assertRedirect(route('admin.posts.index'));
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
