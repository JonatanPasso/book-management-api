<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    public function __construct(string $entity, int $id)
    {
        parent::__construct("{$entity} com ID {$id} não encontrado.");
    }
}
