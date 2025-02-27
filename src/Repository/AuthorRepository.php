<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($registry, Author::class);
    }

    public function save(Author $author): void
    {
        $this->em->persist($author);
    }

    public function remove(Author $author): void
    {
        $this->em->remove($author);
    }

    public function findAllAuthor(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.id, a.name')
            ->getQuery()
            ->getArrayResult();
    }


}
