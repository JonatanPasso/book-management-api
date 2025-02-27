<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Author;
use App\Exception\DuplicateEntryException;
use App\Exception\NoRecordsFoundException;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

class AuthorService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /**
     * @throws DuplicateEntryException
     */
    public function createAuthor(string $name): Author
    {
        $existingAuthor = $this->authorRepository->findOneBy(['name' => $name]);

        if ($existingAuthor) {
            throw new DuplicateEntryException();
        }

        $author = new Author();
        $author->setName($name);
        $this->authorRepository->save($author);
        $this->entityManager->flush();

        return $author;
    }

    public function removeAuthor(Author $author): void
    {
        $this->authorRepository->remove($author);
        $this->entityManager->flush();
    }

    public function getAllAuthors(): array
    {
        return $this->authorRepository->findAllAuthor();
    }

    public function updateAuthor(int $id, array $data): ?Author
    {
        $author = $this->authorRepository->find($id);
        if (!$author) {
            return null;
        }

        $author->setName($data['name']);
        $this->entityManager->flush();

        return $author;
    }

    public function deleteAuthor(int $id): void
    {
        $author = $this->authorRepository->find($id);
        if (!$author) {
            throw new NoRecordsFoundException();
        }
        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }
}
