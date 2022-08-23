<?php

namespace App\Exceptions;

use Exception;

class CannotToggleToPublished extends Exception
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
