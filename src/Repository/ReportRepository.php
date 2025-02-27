<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function findAllGroupedByAuthor()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.authorName', 'ASC')
            ->addOrderBy('b.bookTitle', 'ASC')
            ->getQuery()
            ->getResult();
    }

}