<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Published = 'published';
    case Unpublished = 'unpublished';
    case Pending = 'pending';

    public function canToggleToPublished(): bool
    {
        return match($this) {
            self::Published => false,
            self::Unpublished => true,
            self::Pending => true,
        };
    }

    public function canToggleToUnpublished(): bool
    {
        return match($this) {
            self::Published => true,
            self::Unpublished => false,
            self::Pending => false,
        };
    }
}
