<?php

namespace App\Exceptions;

use Exception;

class CannotToggleToPending extends Exception
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
