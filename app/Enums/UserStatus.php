<?php

namespace App\Enums;

enum UserStatus: string
{
    case Enabled = 'enabled';
    case Disabled = 'disabled';
    case Pending = 'pending';
}
