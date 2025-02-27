<?php

declare(strict_types=1);

namespace App\Exception;

class ServiceException extends \Exception
{
    public function __construct(string $message = "Erro no serviço.", int $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
