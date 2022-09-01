<?php

namespace App\Http\Controllers\Member;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\CommentData;
use App\Http\Controllers\Public\PostController;

class CommentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CommentData $data)
    {
        Post::getEntityById($data->post_id)->comments()->create($data->except('slug')->all());


        return redirect()->action([PostController::class, 'show'], ['post' => $data->slug]);
    }
}
