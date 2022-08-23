<?php

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use App\Events\TagUpdated;
use Database\Seeders\RolePermissionSeeder;
use App\Mail\TagCreatedNotificationMarkdown;
use App\Mail\TagDeletedNotificationMarkdown;
use Inertia\Testing\AssertableInertia as Assert;

use App\Mail\TagUpdatedNotificationMarkdown;
use App\Http\Controllers\Admin\TagController;

uses()->group('admin');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});

/* ------------------------------ @index method ----------------------------- */
it('renders the tag page with tags data', function() {
    Tag::factory()->count(5)->create();
    $tagTitle = Tag::first()->title;

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tag/Index')
        ->has('model', fn(Assert $page) => $page
            ->has('tags', 13)
            ->has('tags', fn(Assert $page) => $page
              ->has('data', 5)
              ->has('data.0', fn(Assert $page) => $page
                ->has('title')
                ->where('title', $tagTitle)
                ->etc())
              ->etc())
            ->etc()
            )
    );
});

/* ----------------------------- @create method ----------------------------- */
it('renders create tag form with Inertia', function() {
    $response = $this->get(action([TagController::class, 'create']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tag/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('tag')
            ->missing('tag.address')
            ->etc()
            )
        );
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit tag form with Inertia', function() {
    $this->withoutExceptionHandling();

    $tag = Tag::factory()->create();
    $title = $tag->title;

    $response = $this->get(action([TagController::class, 'edit'], ['tag' => $tag->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tag/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('tag', fn(Assert $page) => $page
                ->where('title', $title)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @show method ------------------------------ */
it('renders single tag entry with Inertia', function() {
    $this->withoutExceptionHandling();

    $tag = Tag::factory()->create();
    $title = $tag->title;

    $response = $this->get(action([TagController::class, 'show'], ['tag' => $tag->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Tag/Show')
        ->has('model', fn(Assert $page) => $page
            ->has('tag', fn(Assert $page) => $page
                ->where('title', $title)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @store method ----------------------------- */
it('checks the tag data validation and redirect', function() {
    $this->withoutExceptionHandling();

    $tagData = [
        'title' => 'New Tag',
        'meta_title' => 'Meta information',
        'content' => 'Tag content',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.tags.index'));
});

it('checks the session error when validation fails', function() {
    $tagData = [
        'meta_title' => 'Meta information'
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertSessionHasErrors();
});

it('checks the stored tag is in database', function() {
    $tagData = [
        'title' => 'New Tag',
        'meta_title' => 'Meta information',
        'content' => 'Tag content',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('tags', ['title' => 'New Tag']);
});


it('checks the event firing after storing the tag in database', function() {
    Event::fake();
    $tagData = [
        'title' => 'Newest tag',
        'content' => 'Content of the newest tag',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(TagCreated::class);
});

it('checks the mails been queued from admin after storing the tag in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $tagData = [
        'title' => 'Newest tag',
        'content' => 'Content of the newest tag',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(TagCreatedNotificationMarkdown $mail) use ($tagData, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $tagData['title'] ) {
            return false;
        }
        return true;
    });
});

/* ----------------------------- @update method ----------------------------- */
it('checks the validation and redirect at update', function() {
    $tag = Tag::factory()->create();

    $tagData = [
        'title' => 'Updated tag',
        'content' => 'Content of updated tag',
        'id' => $tag->id
    ];

    $response = $this->put(action([TagController::class, 'update'], ['tag' => $tag->slug]), $tagData);
    $tag->refresh();

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([TagController::class, 'edit'], ['tag' => $tag->slug]));
});

it('checks the updated tag is in database', function() {
    $tag = Tag::factory()->create();

    $tagData = [
        'title' => 'Updated tag',
        'content' => 'Content of updated tag',
        'id' => $tag->id
    ];

    $response = $this->put(action([TagController::class, 'update'], ['tag' => $tag->slug]), $tagData);
    $tag->refresh();

    $this->assertDatabaseHas('tags', [
        'title' => 'Updated tag',
    ]);
    $response->assertStatus(302);
    $response->assertRedirect(action([TagController::class, 'edit'], ['tag' => $tag->slug]));
});

it('checks if the tag slug attribute updated according updated title attribute', function() {
    $tag = Tag::factory()->create(['title' => 'New Tag']);
    expect($tag->slug)->toBe('new-tag');
    $this->assertDatabaseHas('tags', ['slug' => $tag->slug]);

    $tagData = [
        'title' => 'Updated Tag',
        'id' => $tag->id
    ];

    $this->put(action([TagController::class, 'update'], ['tag' => $tag->slug]), $tagData);

    $tag->refresh();
    expect($tag->slug)->toBe('updated-tag');
    $this->assertDatabaseHas('tags', ['slug' => $tag->slug]);
});

it('test the content of the TagUpdatedNotificationMarkdown mailable', function() {
    $tag = Tag::factory()->create();

    $mailable = new TagUpdatedNotificationMarkdown('Tag Title', 'Tag Content');

    $mailable->assertSeeInHtml(config('contacts.admin_email'));
    $mailable->assertSeeInHtml('Tag Title');
    $mailable->assertSeeInHtml('Tag Content');
});

it('checks the event firing after updating the tag in database', function() {
    $tag = Tag::factory()->create();
    $tagData = [
        'title' => 'Newest tag',
        'content' => 'Content of the newest tag',
        'id' => $tag->id
    ];
    Event::fake();

    $response = $this->put(action([TagController::class, 'update'], ['tag' => $tag->slug]), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(TagUpdated::class);
});

it('checks the mails been queued from admin after updating the tag in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $tag = Tag::factory()->create();
    $tagData = [
        'title' => 'Newest tag',
        'content' => 'Content of the newest tag',
        'id' => $tag->id
    ];

    $response = $this->put(action([TagController::class, 'update'], ['tag' => $tag->slug]), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(TagUpdatedNotificationMarkdown $mail) use ($tagData, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $tagData['title'] ) {
            return false;
        }
        return true;
    });
});

/* ----------------------------- @delete method ----------------------------- */
it('checks the deletion of entry as well as entry in pivot-table', function() {
    // Arrange #1
    $user = User::factory()->create();
    $post = Post::factory()
        ->has(Category::factory()->count(1))
        ->has(Tag::factory()->count(1))
        ->for($user)
        ->create(['title' => 'New Post Entry']);
    // Assertion #1
    $this->assertDatabaseHas('posts', ['slug' => $post->slug]);
    $this->assertDatabaseHas('post_tag', ['tag_id' => Tag::first()->id, 'post_id' => $post->id]);
    $this->assertDatabaseHas('tags', ['id' => Tag::first()->id, 'title' => Tag::first()->title]);
    $tag = Tag::first();

    $response = $this->delete(action([TagController::class, 'destroy'], ['tag' => Tag::first()->slug]));

    $response->assertRedirect(action([TagController::class, 'index']));
    $this->assertModelMissing($tag);
    $this->assertDatabaseMissing('tags', $tag->toArray());
    $this->assertDatabaseMissing('post_tag', [
        'post_id' => $post->id,
        'tag_id' => $tag->id
    ]);
});



it('checks the event firing after deletion the tag in database', function() {
    $tag = Tag::factory()->create();
    Event::fake();

    $response = $this->delete(action([TagController::class, 'destroy'], ['tag' => $tag->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Event::assertDispatched(TagDeleted::class);
});

it('checks the mails been queued from admin after deleting the tag in database', function() {
    Mail::fake();
    $user = User::factory()->create();
    $user->assignRole('author');
    $tag = Tag::factory()->create();

    $response = $this->delete(action([TagController::class, 'destroy'], ['tag' => $tag->slug]));

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    Mail::assertQueued(function(TagDeletedNotificationMarkdown $mail) use ($tag, $user) {
        if ( ! $mail->hasTo($user->email)) {
            return false;
        }
        if ( $mail->title !== $tag->title ) {
            return false;
        }
        return true;
    });
});

it('test the content of the TagDeletedNotificationMarkdown mailable', function() {
    $tag = Tag::factory()->create();

    $mailable = new TagDeletedNotificationMarkdown('Tag Title', 'Tag Content');

    $mailable->assertSeeInHtml(config('contacts.admin_email'));
    $mailable->assertSeeInHtml('Tag Title');
    $mailable->assertSeeInHtml('Tag Content');
});

