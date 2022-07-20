<?php

namespace App\ViewModels;

use App\Models\Comment;
use App\DataTransferObjects\CommentData;

class UpsertCommentViewModel extends ViewModel
{
    public function __construct(
        public readonly ?Comment $comment = null
    ) {}

    public function comment(): ?CommentData
    {
        if (!$this->comment) {
            return null;
        }

        return CommentData::from($this->comment);
    }
}
