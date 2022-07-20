<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use App\Mail\CategoryCreatedNotificationMarkdown;
use App\Mail\CategoryDeletedNotificationMarkdown;
use App\Mail\CategoryUpdatedNotificationMarkdown;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Database\Eloquent\Factories\Sequence;

uses()->group('admin', 'category');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the category page with category data', function() {
    Category::factory()->count(5)->create();
    $catTitle = Category::first()->title;

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Category/Index')
        ->has('model', fn(Assert $page) => $page
            ->has('categories', 5)
            ->has('categories.0', fn(Assert $page) => $page
                ->where('title', $catTitle)
                ->etc()
            )
        )
    );
});
/* ----------------------------- @create method ----------------------------- */
it('renders create cat form with Inertia', function() {
    $response = $this->get(action([CategoryController::class, 'create']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Category/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('category')
            ->missing('category.address')
            ->etc()
            )
        );
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit category form with Inertia', function() {
    $this->withoutExceptionHandling();

    $cat = Category::factory()->create();
    $title = $cat->title;

    $response = $this->get(action([CategoryController::class, 'edit'], ['category' => $cat->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Category/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('category', fn(Assert $page) => $page
                ->where('title', $title)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @show method ------------------------------ */
it('renders single cat entry with Inertia', function() {
    $this->withoutExceptionHandling();

    $cat = Category::factory()->create();
    $title = $cat->title;

    $response = $this->get(action([CategoryController::class, 'show'], ['category' => $cat->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Category/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('category', fn(Assert $page) => $page
                ->where('title', $title)
                ->etc()
            )
        )
    );
});


/* ------------------------------ @store method ----------------------------- */
it('checks the category data validation and redirect', function() {
    $this->withoutExceptionHandling();

    $catData = [
        'title' => 'New Cat',
        'meta_title' => 'Meta information',
        'content' => 'Cat content',
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.categories.index'));
});

it('checks the incorrect parent_id validation at creation', function() {
    $cat = Category::factory()->create(['id' => 1]);
    $catData = [
        'title' => 'New Cat',
        'meta_title' => 'Meta information',
        'content' => 'Cat content',
        'parent_id' => 5
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertSessionHasErrors();
});

it('checks the stored cat is in database', function() {
    $catData = [
        'title' => 'New Cat',
        'meta_title' => 'Meta information',
        'content' => 'Cat content',
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('categories', ['title' => 'New Cat']);
});

it('checks the event firing after storing the cat in database', function() {
    Event::fake();
    $catData = [
        'title' => 'Newest cat',
        'content' => 'Content of the newest cat',
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(CategoryCreated::class);
});

it('checks the mails been queued from admin after storing the cat in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $catData = [
        'title' => 'Newest cat',
        'content' => 'Content of the newest cat',
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(CategoryCreatedNotificationMarkdown $mail) use ($catData, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $catData['title'] ) {
            return false;
        }
        return true;
    });
});

it('test the content of the CategoryCreatedNotificationMarkdown mailable', function() {
    $tag = Category::factory()->create();

    $mailable = new CategoryCreatedNotificationMarkdown('Category Title', 'Category Content');

    $mailable->assertSeeInHtml(config('contacts.admin_email'));
    $mailable->assertSeeInHtml('Category Title');
    $mailable->assertSeeInHtml('Category Content');
});

/* ----------------------------- @update method ----------------------------- */
it('checks the validation and redirect at update', function() {
    $cat = Category::factory()->create();

    $catData = [
        'title' => 'Updated category',
        'content' => 'Content of updated category',
        'id' => $cat->id
    ];

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);
    $cat->refresh();

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([CategoryController::class, 'edit'], ['category' => $cat->slug]));
});

it('checks the incorrect parent_id validation at update', function() {
    $cat = Category::factory()->count(2)->state(new Sequence(['id' => 1], ['id' => 3]))->create();

    $catData = [
        'title' => 'Updated Category',
        'parent_id' => 5,
        'id' => $cat->find(3)->id
    ];

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->find(3)->slug]), $catData);

    $response->assertSessionHasErrors();
});

it('checks the updated cat is in database', function() {
    $cat = Category::factory()->create();

    $catData = [
        'title' => 'Updated category',
        'content' => 'Content of updated category',
        'id' => $cat->id
    ];

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);
    $cat->refresh();

    $this->assertDatabaseHas('categories', [
        'title' => 'Updated category',
    ]);
    $response->assertStatus(302);
    $response->assertRedirect(action([CategoryController::class, 'edit'], ['category' => $cat->slug]));
});

it('checks if the cat slug attribute updated according updated title attribute', function() {
    $cat = Category::factory()->create(['title' => 'New Cat']);
    expect($cat->slug)->toBe('new-cat');
    $this->assertDatabaseHas('categories', ['slug' => $cat->slug]);

    $catData = [
        'title' => 'Updated Cat',
        'id' => $cat->id
    ];

    $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);

    $cat->refresh();
    expect($cat->slug)->toBe('updated-cat');
    $this->assertDatabaseHas('categories', ['slug' => $cat->slug]);
});

it('test the content of the CategoryUpdatedNotificationMarkdown mailable', function() {
    $cat = Category::factory()->create();

    $mailable = new CategoryUpdatedNotificationMarkdown('Category Title', 'Category Content');

    $mailable->assertSeeInHtml(config('contacts.admin_email'));
    $mailable->assertSeeInHtml('Category Title');
    $mailable->assertSeeInHtml('Category Content');
});

it('checks the event firing after updating the cat in database', function() {
    $cat = Category::factory()->create();
    $catData = [
        'title' => 'Newest cat',
        'content' => 'Content of the newest cat',
        'id' => $cat->id
    ];
    Event::fake();

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(CategoryUpdated::class);
});

it('checks the mails been queued from admin after updating the cat in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $cat = Category::factory()->create();
    $catData = [
        'title' => 'Newest cat',
        'content' => 'Content of the newest cat',
        'id' => $cat->id
    ];

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(CategoryUpdatedNotificationMarkdown $mail) use ($catData, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $catData['title'] ) {
            return false;
        }
        return true;
    });
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of cat entry as well as entry in pivot-table', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(1))
        ->has(Tag::factory()->count(1))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    // Assertion #1
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    $this->assertDatabaseHas('category_post', ['category_id' => Category::first()->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('categories', ['id' => Category::first()->id, 'title' => Category::first()->title]);
    $cat = Category::first();

    $response = $this->delete(action([CategoryController::class, 'destroy'], ['category' => Category::first()->slug]));

    $response->assertRedirect(action([CategoryController::class, 'index']));
    $this->assertModelMissing($cat);
    $this->assertDatabaseMissing('categories', $cat->toArray());
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $post->id,
        'category_id' => $cat->id
    ]);
});

it('checks the event firing after deletion the category in database', function() {
    $cat = Category::factory()->create();
    Event::fake();

    $response = $this->delete(action([CategoryController::class, 'destroy'], ['category' => $cat->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(CategoryDeleted::class);
});

it('checks the mails been queued from admin after deleting the category in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $cat = Category::factory()->create();

    $response = $this->delete(action([CategoryController::class, 'destroy'], ['category' => $cat->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(CategoryDeletedNotificationMarkdown $mail) use ($cat, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $cat->title ) {
            return false;
        }
        return true;
    });
});

it('test the content of the CategoryDeletedNotificationMarkdown mailable', function() {
    $cat = Category::factory()->create();

    $mailable = new CategoryDeletedNotificationMarkdown('Cat Title', 'Cat Content');

    $mailable->assertSeeInHtml(config('contacts.admin_email'));
    $mailable->assertSeeInHtml('Cat Title');
    $mailable->assertSeeInHtml('Cat Content');
});
