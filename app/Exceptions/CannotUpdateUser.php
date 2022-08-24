<?php

namespace App\Exceptions;

use Exception;

class CannotUpdateUser extends Exception
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
