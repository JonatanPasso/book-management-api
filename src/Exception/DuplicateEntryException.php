<?php

declare(strict_types=1);

namespace App\Exception;

class DuplicateEntryException extends \Exception
{
    public function __construct(string $message = "Registro duplicado.", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
