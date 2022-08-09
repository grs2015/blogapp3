<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Gallery;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ImageController;
use function Spatie\PestPluginTestTime\testTime;

uses()->group('admin', 'images');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});

/* ----------------------------- @delete method ----------------------------- */
it('logged-in as an admin, checks the deletion of hero-image and all its thumbnails from DB and filesystem', function() {

    //Assign
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
    //Action
    $response = $this->post(action([PostController::class, 'store']), $postData);
    // Assert
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::assertExists('uploads/640-480-2022-01-01-00-00-00-test.jpg');
    $urlEntry = '/storage/uploads/640-480-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/HiRes-2022-01-01-00-00-00-test.jpg'.
                ','.'/storage/uploads/LoRes-2022-01-01-00-00-00-test.jpg';
    $this->assertDatabaseHas('posts', [
        'hero_image' => $urlEntry
    ]);
    $response->assertStatus(302);

    // Assign
    $postSlug = Post::find(1)->slug;
    $postId = Post::find(1)->id;

    //Action
    $response = $this->post(action([ImageController::class, 'delete_heroimage']), [ 'post_id' => $postId, 'slug' => $postSlug ]);

    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/640-480-2022-01-01-00-00-00-test.jpg');

    $this->assertDatabaseMissing('posts', [
        'hero_image' => $urlEntry
    ]);

    $response->assertStatus(302);
});


it('logged-in as an admin, checks the deletion of gallery image and all its thumbnails from DB and filesystem', function() {

    //Assign
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
    //Action
    $response = $this->post(action([PostController::class, 'store']), $postData);
    // Assert
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_1.jpg');
    Storage::assertExists('uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    Storage::assertExists('uploads/LoRes-2022-01-01-00-00-00-test_2.jpg');

    expect(Post::first()->galleries()->first()->original)->toEqual('/storage/uploads/HiRes-2022-01-01-00-00-00-test_1.jpg');
    expect(Post::first()->galleries()->first()->thumbs)->toEqual('/storage/uploads/640-480-2022-01-01-00-00-00-test_1.jpg');

    $response->assertStatus(302);

    // Assign
    $postSlug = Post::find(1)->slug;
    $postId = Post::find(1)->id;

    //Action
    $response = $this->post(action([ImageController::class, 'delete_galleryimage']), [ 'post_id' => $postId, 'slug' => $postSlug, 'image_idx' => 1 ]);

    Storage::disk('public')->assertMissing('uploads/HiRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/LoRes-2022-01-01-00-00-00-test.jpg');
    Storage::disk('public')->assertMissing('uploads/640-480-2022-01-01-00-00-00-test.jpg');

    expect(Post::first()->galleries()->find(1)->original)->not()->toBe('/storage/uploads/HiRes-2022-01-01-00-00-00-test_2.jpg');
    expect(Post::first()->galleries()->find(2))->toBeNull();

    $response->assertStatus(302);
});
