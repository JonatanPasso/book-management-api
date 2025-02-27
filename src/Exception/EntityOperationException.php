<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class EntityOperationException extends HttpException
{
    public function __construct(
        string $message = "Erro ao processar a operação.",
        int $statusCode = 500,
        \Throwable $previous = null
    ) {
        parent::__construct($statusCode, $message, $previous);
    }
}
