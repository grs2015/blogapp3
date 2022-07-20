<?php

use App\Enums\CommentStatus;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Admin\PostCommentController;
use Inertia\Testing\AssertableInertia as Assert;


uses()->group('admin', 'postcomment');

beforeEach(function() {
    $this->seed(RolePermissionSeeder::class);
    loginAsAdmin();
});
/* ------------------------------ @index method ----------------------------- */
it('renders the comments page with Inertia', function() {
    $post = Post::factory()->hasComments(3)->create();
    $commentTitle = $post->comments[0]->title;

    $response = $this->get(action([PostCommentController::class, 'index'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Comment/Index')
        ->has('model', fn(Assert $page) => $page
            ->has('comments', 3)
            ->has('comments.0', fn(Assert $page) => $page
                ->where('title', $commentTitle)
                ->etc()
            )
        )
    );
});

/* ----------------------------- @create method ----------------------------- */
it('renders create comment form with Inertia', function() {
    $post = Post::factory()->create();
    $response = $this->get(action([PostCommentController::class, 'create'], ['post' => $post->slug]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Comment/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('comment')
            ->missing('comment.address')
            ->etc()
            )
        );
});

/* ------------------------------ @edit method ------------------------------ */
it('renders edit form for single comment with Inertia', function() {
    $post = Post::factory()->hasComments(3)->create();
    $comment = Comment::first();
    $commentTitle = $comment->title;

    $response = $this->get(action([PostCommentController::class, 'edit'], ['post' => $post->slug, 'comment' => $comment->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Comment/Form')
        ->has('model', fn(Assert $page) => $page
            ->has('comment', fn(Assert $page) => $page
                ->where('title', $commentTitle)
                ->etc()
            )
        )
    );
});

/* ------------------------------ @show method ------------------------------ */
it('renders single comment entry with Inertia', function() {
    $this->withoutExceptionHandling();

    $post = Post::factory()->hasComments(3)->create();
    $comment = Comment::first();
    $commentTitle = $comment->title;

    $response = $this->get(action([PostCommentController::class, 'show'], ['post' => $post->slug, 'comment' => $comment->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Comment/Show')
        ->has('model', fn(Assert $page) => $page
            ->has('comment', fn(Assert $page) => $page
                ->where('title', $commentTitle)
                ->etc()
            )
        )
    );
});



/* ------------------------------ @store method ----------------------------- */
it('checks the comment data validation and redirect', function() {
    $this->withoutExceptionHandling();
    $post = Post::factory()->create();
    $commentData = [
        'title' => 'New Comment',
        'content' => 'Content of comment',
        'published_at' => now(),
        'post_id' => $post->id
    ];

    $response = $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.posts.comments.index', ['post' => $post->slug]));
});

it('checks the session error when validation fails at storing', function() {
    $post = Post::factory()->create();
    $commentData = [
        'content' => 'Content of comment',
    ];

    $response = $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $response->assertSessionHasErrors();
});

it('checks the stored comment data has some predefined properties and resides in database', function() {
    $post = Post::factory()->create();
    $time = now();
    $commentData = [
        'title' => 'New Comment',
        'content' => 'Content of comment',
        'published_at' => $time,
        'post_id' => $post->id
    ];

    $this->post(action([PostCommentController::class, 'store'],['post' => $post->slug]), $commentData);

    $this->assertDatabaseHas('comments', [
        'status' => 'pending',
        'title' => 'New Comment',
    ]);
});

/* ----------------------------- @update method ----------------------------- */
it('checks the comment data for validation and redirect at update', function() {
    $post = Post::factory()->hasComments(3)->create();
    $commentData = [
        'title' => 'Updated comment',
        'content' => 'Updated comment content',
        'published_at' => now()->addHours(5),
        'post_id' => $post->id,
        'id' => Comment::first()->id
    ];

    $response = $this->put(action([PostCommentController::class, 'update'], ['post' => $post->slug, 'comment' => Comment::first()->id]), $commentData);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);
    $response->assertRedirect(action([PostCommentController::class, 'edit'], ['post' => $post->slug, 'comment' => Comment::first()->id]));
});

it('checks the updated comment is in database', function() {
    $post = Post::factory()->hasComments(3)->create();
    $commentData = [
        'title' => 'Updated comment',
        'content' => 'Updated comment content',
        'published_at' => now()->addHours(5),
        'post_id' => $post->id,
        'id' => Comment::first()->id
    ];

    $this->put(action([PostCommentController::class, 'update'], ['post' => $post->slug, 'comment' => Comment::first()->id]), $commentData);

    $this->assertDatabaseHas('comments', ['title' => 'Updated comment']);
});

it('checks the session error when validation fails at update', function() {
    $post = Post::factory()->hasComments(3)->create();
    $commentData = [
        'content' => 'Updated comment content',
        'published_at' => now()
    ];

    $response = $this->put(action([PostCommentController::class, 'update'], ['post' => $post->slug, 'comment' => Comment::first()->id]), $commentData);

    $response->assertSessionHasErrors();
});

/* ----------------------------- @destroy method ---------------------------- */
it('checks the deletion of entry', function() {
    $post = Post::factory()->hasComments(3)->create();
    $comment = Comment::first();

    $response = $this->delete(action([PostCommentController::class, 'destroy'], ['post' => $post->slug, 'comment' => Comment::first()->id]));

    $response->assertRedirect(route('admin.posts.comments.index', ['post' => $post->slug]));
    $this->assertModelMissing($comment);
    $this->assertDatabaseMissing('comments', $comment->toArray());
});
