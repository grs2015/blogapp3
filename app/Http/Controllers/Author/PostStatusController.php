<?php

namespace App\Http\Controllers\Author;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostStatusRequest;
use App\Actions\Blog\ChangePostStatusAction;

class PostStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PostStatusRequest $request)
    {
        ChangePostStatusAction::execute($request);

        return redirect()->action([PostController::class, 'index'], ['page' => $request->page, 'per_page' => $request->per_page, 'search' => $request->search]);
    }
}
