<?php

declare(strict_types=1);

namespace App\Exception;

class SubjectNotFoundException extends ServiceException
{
    public function __construct(string $message = "Nenhum assunto encontrado.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
