<?php

declare(strict_types=1);

namespace App\Exception;

class AuthorNotFoundException extends ServiceException
{
    public function __construct(string $message = "Nenhum autor encontrado.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
