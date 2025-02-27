<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $em,
    ) {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $book): void
    {
        $this->em->persist($book);
    }

    public function findAllBooks(): array
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'a', 's')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.subjects', 's')
            ->getQuery()
            ->getArrayResult();
    }
}
