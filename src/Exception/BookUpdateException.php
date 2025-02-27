<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BookUpdateException extends HttpException
{
    public function __construct(string $message = 'Erro ao atualizar o livro', int $statusCode = 500)
    {
        parent::__construct($statusCode, $message);
    }
}
