<?php

namespace App\Enums;

enum UserStatus: string
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';
    case Pending = 'pending';

    public function canBeDeleted(): bool
    {
        return match($this) {
            self::Enabled => false,
            self::Disabled => false,
            self::Pending => true
        };
    }

    public function canBeUpdated(): bool
    {
        return match($this) {
            self::Enabled => false,
            self::Disabled => false,
            self::Pending => true
        };
    }

    public function canDoActions(): bool
    {
        return match($this) {
            self::Enabled => true,
            self::Disabled => false,
            self::Pending => false
        };
    }
}


