<?php

namespace App\Http\Controllers\Member;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Controllers\Public\PostController;

class PostCommentController extends Controller
{
    public function create()
    {
        $this->authorize('create', Comment::class);

        return view('member.comment.create');
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $this->authorize('create', Comment::class);

        $validated = $request->validated();
        $validated['published'] = Comment::UNPUBLISHED;

        $post->comments()->create($validated);

        return redirect()->action([PostController::class, 'show'], ['post' => $post->slug]);
    }
}
