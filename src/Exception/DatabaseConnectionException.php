<?php

declare(strict_types=1);

namespace App\Exception;

class DatabaseConnectionException extends \Exception
{
    public function __construct(string $message = "Erro de conexão com o banco de dados.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
