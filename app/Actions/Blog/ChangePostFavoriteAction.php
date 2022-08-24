<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Http\Requests\FavoriteRequest;
use App\Exceptions\CannotCompleteAction;

class ChangePostFavoriteAction
{
    public static function execute(FavoriteRequest $request)
    {
        $post = Post::getEntityById($request->id);

        if ($request->user()->can('change favorite')) {
            $request->favorite === 'favorite' ? $post->markAsFavorite() : $post->markAsNonFavorite();
        } else { throw CannotCompleteAction::because("User doesn't have appropriate permissions"); }
    }
}
