<?php

declare(strict_types=1);

namespace App\Exception;

class DataRetrievalException extends ServiceException
{
    public function __construct(string $message = "Erro ao recuperar dados.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
