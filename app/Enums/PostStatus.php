<?php

namespace App\Enums;

enum PostStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
    case Pending = 'pending';
    case Unpublished = 'unpublished';

    public function canToggleToPending(): bool
    {
        return match($this) {
            self::Published => 'false',
            self::Draft => 'true',
            self::Pending => 'false',
            self::Unpublished => 'false'
        };
    }

    public function canToggleToPublished(): bool
    {
        return match($this) {
            self::Published => 'false',
            self::Draft => 'false',
            self::Pending => 'true',
            self::Unpublished => 'true'
        };
    }

    public function canToggleToUnpublished(): bool
    {
        return match($this) {
            self::Published => 'true',
            self::Draft => 'false',
            self::Pending => 'false',
            self::Unpublished => 'false'
        };
    }
}
