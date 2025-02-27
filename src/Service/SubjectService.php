<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Subject;
use App\Exception\DuplicateEntryException;
use App\Exception\NoRecordsFoundException;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubjectService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SubjectRepository $subjectRepository,
    ) {
    }

    /**
     * @throws DuplicateEntryException
     */
    public function createSubject(string $description): Subject
    {
        $existingSubject = $this->subjectRepository->findOneBy(['description' => $description]);

        if ($existingSubject) {
            throw new DuplicateEntryException();
        }

        $subject = new Subject();
        $subject->setDescription($description);
        $this->subjectRepository->save($subject);
        $this->entityManager->flush();

        return $subject;
    }

    public function removeSubject(Subject $subject): void
    {
        $this->subjectRepository->remove($subject);
        $this->entityManager->flush();
    }

    public function getAllSubjects(): array
    {
        return $this->subjectRepository->findAllSubjects();
    }

    public function updateSubject(int $id, array $data): ?Subject
    {
        $subject = $this->subjectRepository->find($id);
        if (!$subject) {
            return null;
        }

        $subject->setDescription($data['description']);
        $this->entityManager->flush();

        return $subject;
    }

    /**
     * @throws NoRecordsFoundException
     */
    public function deleteSubject(int $id): void
    {
        $subject = $this->subjectRepository->find($id);
        if (!$subject) {
            throw new NoRecordsFoundException();
        }
        $this->entityManager->remove($subject);
        $this->entityManager->flush();
    }
}
