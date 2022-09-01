<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ViewModels\UpsertPostViewModel;
use App\ViewModels\GetCommentsViewModel;
use App\Http\Requests\CommentStatusRequest;
use App\Actions\Blog\ChangeCommentStatusAction;

class CommentStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CommentStatusRequest $request)
    {
        $post = ChangeCommentStatusAction::execute($request);

        return redirect()->action([PostController::class, 'edit'], ['post' => $post->slug]);
    }
}
