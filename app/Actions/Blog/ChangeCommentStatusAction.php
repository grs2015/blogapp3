<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Models\Comment;
use App\Exceptions\CannotToggleToPublished;
use App\Http\Requests\CommentStatusRequest;
use App\Exceptions\CannotToggleToUnublished;

class ChangeCommentStatusAction
{
    public static function execute(CommentStatusRequest $request): Post
    {
        $comment = Comment::getEntityById($request->id);

        if ($request->status == 'published') {
            $comment->status->canToggleToPublished() ? $comment->markAsPublished() : throw CannotToggleToPublished::because("Comment doesn't have initial Pending status");
        }

        if ($request->status == 'unpublished') {
            $comment->status->canToggleToUnpublished() ? $comment->markAsUnpublished() : throw CannotToggleToUnublished::because("Comment doesn't have initial Published status");
        }

        return $comment->post;
    }
}
