<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Exceptions\CannotCompleteAction;
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
            if ($request->user()->can('change post status to pending')) {
                $post->status->canToggleToPending() ? $post->markAsPending() : throw CannotToggleToPending::because("Post doesn't have initial Draft status");
            } else { throw CannotCompleteAction::because("User doesn't have appropriate permissions"); }
        }

        if ($request->status == 'published') {
            if ($request->user()->can('change post status to published')) {
                $post->status->canToggleToPublished() ? $post->markAsPublished() : throw CannotToggleToPublished::because("Post doesn't have initial Pending/Unpublished status");
            } else { throw CannotCompleteAction::because("User doesn't have appropriate permissions"); }
        }

        if ($request->status == 'unpublished') {
            if ($request->user()->can('change post status to unpublished')) {
            $post->status->canToggleToUnpublished() ? $post->markAsUnpublished() : throw CannotToggleToUnublished::because("Post doesn't have initial Published status");
            } else { throw CannotCompleteAction::because("User doesn't have appropriate permissions"); }
        }
    }
}
