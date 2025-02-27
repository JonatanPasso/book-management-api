<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        protected EntityManagerInterface $em
    ) {
        parent::__construct($registry, Subject::class);
    }

    public function save(Subject $subject): void
    {
        $this->em->persist($subject);
    }

    public function remove(Subject $subject): void
    {
        $this->em->remove($subject);
    }

    public function findAllSubjects(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id, s.description')
            ->getQuery()
            ->getArrayResult();
    }

}
