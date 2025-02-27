<?php

declare(strict_types=1);

namespace App\Exception;

class UnexpectedApplicationException extends \Exception
{
    public function __construct(string $message = "Ocorreu um erro inesperado.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
