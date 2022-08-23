<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Http\Requests\FavoriteRequest;

class ChangePostFavoriteAction
{
    public static function execute(FavoriteRequest $request)
    {
        $post = Post::getEntityById($request->id);

        $request->favorite === 'favorite' ? $post->markAsFavorite() : $post->markAsNonFavorite();
    }
}
