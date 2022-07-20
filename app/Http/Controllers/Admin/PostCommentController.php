<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Inertia\Inertia;
use App\Models\Comment;
use App\Services\CacheService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\DataTransferObjects\CommentData;
use App\ViewModels\GetCommentsViewModel;
use App\Actions\Blog\UpsertCommentAction;
use App\Http\Requests\StoreCommentRequest;
use App\ViewModels\UpsertCommentViewModel;
use App\Http\Requests\UpdateCommentRequest;
use App\Interfaces\CommentRepositoryInterface;

class PostCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post, CacheService $cacheService)
    {
        return Inertia::render('Comment/Index', [
            'model' => new GetCommentsViewModel($cacheService, $post)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Comment/Form', [
            'model' => new UpsertCommentViewModel()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, Comment $comment)
    {
        return Inertia::render('Comment/Form', [
            'model' => new UpsertCommentViewModel($comment)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post, Comment $comment)
    {
        return Inertia::render('Comment/Show', [
            'model' => new UpsertCommentViewModel($comment)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentData $data, Post $post)
    {
        UpsertCommentAction::execute($data);

        return redirect()->action([PostCommentController::class, 'index'], ['post' => $post->slug]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommentData $data, Post $post)
    {
        UpsertCommentAction::execute($data);

        return redirect()->action([PostCommentController::class, 'edit'], ['post' => $post->slug, 'comment' => $data->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, Comment $comment)
    {
        $comment->delete();

        return redirect()->action([PostCommentController::class, 'index'], ['post' => $post->slug]);
    }
}
