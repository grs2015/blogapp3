<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Http\Requests\PostStatusRequest;
use App\Exceptions\CannotToggleToPending;
use App\Exceptions\CannotToggleToPublished;
use App\Exceptions\CannotToggleToUnublished;

class ChangePostStatusAction
{
    public static function execute(PostStatusRequest $request)
    {
        $post = Post::getEntityById($request->id);

        if ($request->status == 'pending') {
            $post->status->canToggleToPending() ? $post->markAsPending() : throw CannotToggleToPending::because("Post doesn't have initial Draft status");
        }

        if ($request->status == 'published') {
            $post->status->canToggleToPublished() ? $post->markAsPublished() : throw CannotToggleToPublished::because("Post doesn't have initial Pending/Unpublished status");
        }

        if ($request->status == 'unpublished') {
            $post->status->canToggleToUnpublished() ? $post->markAsUnpublished() : throw CannotToggleToUnublished::because("Post doesn't have initial Published status");
        }
    }
}
