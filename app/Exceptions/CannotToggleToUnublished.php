<?php

namespace App\Exceptions;

use Exception;

class CannotToggleToUnublished extends Exception
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
