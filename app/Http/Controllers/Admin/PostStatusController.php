<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Enums\PostStatus;
use Illuminate\Http\Request;
use App\Permissions\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostStatusRequest;
use App\Exceptions\CannotToggleToPending;
use App\Exceptions\CannotToggleToPublished;
use App\Actions\Blog\ChangePostStatusAction;
use App\Exceptions\CannotToggleToUnublished;

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

        return redirect()->action([PostController::class, 'index'], ['page' => $request->page, 'per_page' => $request->per_page]);
    }
}
