<?php

namespace App\Actions\Blog;

use App\Models\Comment;
use App\DataTransferObjects\CommentData;

class UpsertCommentAction
{
    public static function execute(CommentData $data): Comment
    {
        return Comment::updateOrCreate(['id' => $data->id], $data->all());
    }
}
