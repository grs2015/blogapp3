<?php

namespace App\Exceptions;

use Exception;

class CannotBeDeleted extends Exception
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
