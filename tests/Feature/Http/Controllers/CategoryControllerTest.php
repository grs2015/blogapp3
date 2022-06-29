<?php

use App\Models\User;
use App\Models\Category;
use App\Events\CategoryCreated;
use App\Events\CategoryUpdated;
use App\Http\Controllers\CategoryController;
use App\Mail\CategoryCreatedNotificationMarkdown;
use App\Mail\CategoryUpdatedNotificationMarkdown;
use Illuminate\Database\Eloquent\Factories\Sequence;

uses()->group('Cat');

/* ------------------------------ @index method ----------------------------- */
it('renders the cat page with cats data', function() {
    $tags = Category::factory()->count(5)->create();
    $catTitle = Category::inRandomOrder()->first()->title;

    $response = $this->get(action([CategoryController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All cats:');
    $response->assertSee($catTitle);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create cat form', function() {
    $this->get('/categories/create')->assertSee('Form for cat creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $catData = [
        'title' => 'New Cat',
        'meta_title' => 'Meta information',
        'content' => 'Cat content',
    ];

    $response = $this->post(action([CategoryController::class, 'store']), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('categories.index'));
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
    $user = User::factory()->author()->create();
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

/* ------------------------------ @show method ------------------------------ */
it('renders single cat entry by given slug', function() {
    $cat = Category::factory()->create();

    $response = $this->get(action([CategoryController::class, 'show'], ['category' => $cat->slug]));

    $response->assertSee($cat->title);
    $response->assertSee($cat->content);
    $response->assertSee($cat->slug);
    $response->assertDontSee($cat->summary);
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for cat by given slug', function() {
    $cat = Category::factory()->create();

    $response = $this->get(action([CategoryController::class, 'edit'], ['category' => $cat->slug]));

    $response->assertSee($cat->title);
    $response->assertSee($cat->content);
    $response->assertSee($cat->slug);
    $response->assertDontSee($cat->summary);
});
/* ----------------------------- @update method ----------------------------- */
it('checks the validation and redirect at update', function() {
    $cat = Category::factory()->create();

    $catData = [
        'title' => 'Updated category',
        'content' => 'Content of updated category'
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
        'parent_id' => 5
    ];

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->find(3)->slug]), $catData);

    $response->assertSessionHasErrors();
});

it('checks the updated cat is in database', function() {
    $cat = Category::factory()->create();

    $catData = [
        'title' => 'Updated category',
        'content' => 'Content of updated category'
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
    ];
    Event::fake();

    $response = $this->put(action([CategoryController::class, 'update'], ['category' => $cat->slug]), $catData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(CategoryUpdated::class);
});

it('checks the mails been queued from admin after updating the cat in database', function() {
    Mail::fake();
    $user = User::factory()->author()->create();
    $cat = Category::factory()->create();
    $catData = [
        'title' => 'Newest cat',
        'content' => 'Content of the newest cat',
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
