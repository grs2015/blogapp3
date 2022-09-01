<?php

namespace App\Actions\Blog;

use App\Models\Post;
use App\Exceptions\CannotBeDeleted;

class DeletePostAction
{
    public static function execute(Post $post)
    {
        // $post->status->canBeDeleted() ? $post->delete() : throw CannotBeDeleted::because("Post doesn't have Draft/Pending status");
        $post->delete();
    }
}
