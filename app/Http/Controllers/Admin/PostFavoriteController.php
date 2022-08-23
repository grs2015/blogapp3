<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Blog\ChangePostFavoriteAction;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteRequest;

class PostFavoriteController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(FavoriteRequest $request)
    {
        ChangePostFavoriteAction::execute($request);

        return redirect()->action([PostController::class, 'index'], ['page' => $request->page, 'per_page' => $request->per_page]);
    }
}
