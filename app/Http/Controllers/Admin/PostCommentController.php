<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Interfaces\CommentRepositoryInterface;

class PostCommentController extends Controller
{

    public function __construct(
        private CommentRepositoryInterface $commentRepository
    ) {  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
        $comments = $this->commentRepository->getAllEntries($post->id);

        return view('comment.index', compact('comments', 'post'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('comment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        $validated = $request->validated();
        $validated['published'] = Comment::UNPUBLISHED;

        $this->commentRepository->createEntry($post->id, $validated);

        return redirect()->action([PostCommentController::class, 'index'], ['post' => $post->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Comment $comment)
    {
        $comment = $this->commentRepository->getEntryById($post->id, $comment->id);

        return view('comment.show', compact('post', 'comment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, Comment $comment)
    {
        $comment = $this->commentRepository->getEntryById($post->id, $comment->id);

        return view('comment.edit', compact('post', 'comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentRequest $request, Post $post, Comment $comment)
    {
        $validated = $request->validated();

        $this->commentRepository->updateEntry($post->id, $comment->id, $validated);

        return redirect()->action([PostCommentController::class, 'edit'], ['post' => $post->slug, 'comment' => $comment->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, Comment $comment)
    {
        $this->commentRepository->deleteEntry($post->id, $comment->id);

        return redirect()->action([PostCommentController::class, 'index'], ['post' => $post->slug]);
    }
}
