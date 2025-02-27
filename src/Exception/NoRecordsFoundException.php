<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class NoRecordsFoundException extends \Exception
{
    public function __construct(
        string $message = "Nenhum registro encontrado.",
        int $code = Response::HTTP_NOT_FOUND,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
