<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Published = 'published';
    case Unpublished = 'unpublished';
    case Pending = 'pending';
}
