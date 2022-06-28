<?php

use App\Models\Tag;
use App\Models\User;
use App\Events\TagCreated;
use App\Http\Controllers\TagController;
use App\Mail\TagCreatedNotificationMarkdown;

uses()->group('TagController');

/* ------------------------------ @index method ----------------------------- */
it('renders the tag page with tags data', function() {
    $tags = Tag::factory()->count(5)->create();
    $tagTitle = Tag::inRandomOrder()->first()->title;

    $response = $this->get(action([TagController::class, 'index']));

    $response->assertOk();
    $response->assertSee('All tags');
    $response->assertSee($tagTitle);
});

/* ----------------------------- @create method ----------------------------- */
it('renders create tag form', function() {
    $this->get('/tags/create')->assertSee('Form for tag creation');
});

/* ------------------------------ @store method ----------------------------- */
it('checks the validation and redirect', function() {
    $tagData = [
        'title' => 'New Tag',
        'meta_title' => 'Meta information',
        'content' => 'Tag content',
    ];

    $response = $this->post(action([TagController::class, 'store']), $tagData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('tags.index'));
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
    $user = User::factory()->author()->create();
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

/* ------------------------------ @show method ------------------------------ */
it('renders single tag entry by given slug', function() {
    $tag = Tag::factory()->create();

    $response = $this->get(action([TagController::class, 'show'], ['tag' => $tag->slug]));

    $response->assertSee($tag->title);
    $response->assertSee($tag->content);
    $response->assertSee($tag->slug);
    $response->assertDontSee($tag->summary);
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for tag by given slug', function() {
    $tag = Tag::factory()->create();

    $response = $this->get(action([TagController::class, 'edit'], ['tag' => $tag->slug]));

    $response->assertSee($tag->title);
    $response->assertSee($tag->content);
    $response->assertSee($tag->slug);
    $response->assertDontSee($tag->summary);
});
