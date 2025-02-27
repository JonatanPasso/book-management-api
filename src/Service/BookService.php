<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Exception\DuplicateEntryException;
use App\Exception\EntityNotFoundException;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public function __construct(
        protected AuthorRepository $authorRepository,
        protected SubjectRepository $subjectRepository,
        protected BookRepository $bookRepository,
        protected EntityManagerInterface $entityManager,
    ) {
    }    

    /**
     * @throws DuplicateEntryException
     */
    public function createBook(array $data): Book
    {
        $this->entityManager->beginTransaction();

        $existingBook = $this->bookRepository->findOneBy(['title' => $data['title']]);

        if ($existingBook) {
            throw new DuplicateEntryException('Livro com este título já existe.');
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setPublisher($data['publisher']);
        $book->setEdition($data['edition']);
        $book->setPublicationYear($data['publicationYear']);
        $book->setPrice($data['price']);

        if (!empty($data['authors'])) {
            foreach ($data['authors'] as $authorId) {
                $author = $this->authorRepository->find($authorId['id']);
                if (!$author) {
                    throw new EntityNotFoundException('Autor', $authorId);
                }
                $book->addAuthor($author);
            }
        }

        if (!empty($data['subjects'])) {
            foreach ($data['subjects'] as $subjectId) {
                $subject = $this->subjectRepository->find($subjectId['id']);
                if (!$subject) {
                    throw new EntityNotFoundException('Assunto', $subjectId);
                }
                $book->addSubject($subject);
            }
        }

        $this->bookRepository->save($book);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $book;
    }

    /**
     * @throws DuplicateEntryException
     */
    public function updateBook(int $bookId, array $data): Book
    {
        $this->entityManager->beginTransaction();

        $book = $this->bookRepository->find($bookId);

        if (!$book) {
            throw new EntityNotFoundException('Livro', $bookId);
        }

        if (isset($data['title'])) {
            $existingBook = $this->bookRepository->findOneBy(['title' => $data['title']]);
            if ($existingBook && $existingBook->getId() !== $bookId) {
                throw new DuplicateEntryException('Livro com este título já existe.');
            }
            $book->setTitle($data['title']);
        }

        if (isset($data['publisher'])) {
            $book->setPublisher($data['publisher']);
        }

        if (isset($data['edition'])) {
            $book->setEdition($data['edition']);
        }

        if (isset($data['publicationYear'])) {
            $book->setPublicationYear($data['publicationYear']);
        }

        if (isset($data['price'])) {
            $book->setPrice($data['price']);
        }

        if (isset($data['authors'])) {
            $book->clearAuthors();
            foreach ($data['authors'] as $authorId) {
                $author = $this->authorRepository->find($authorId['id']);
                if (!$author) {
                    throw new EntityNotFoundException('Autor', $authorId);
                }
                $book->addAuthor($author);
            }
        }

        if (isset($data['subjects'])) {
            $book->clearSubjects();
            foreach ($data['subjects'] as $subjectId) {
                $subject = $this->subjectRepository->find($subjectId['id']);
                if (!$subject) {
                    throw new EntityNotFoundException('Assunto', $subjectId);
                }
                $book->addSubject($subject);
            }
        }

        $this->bookRepository->save($book);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $book;
    }

    public function getAllBooks(): array
    {
        return $this->bookRepository->findAllBooks();
    }

    public function deleteBook(int $id): void
    {
        $this->entityManager->beginTransaction();

        $book = $this->bookRepository->find($id);

        if (!$book) {
            throw new EntityNotFoundException("Livro", $id);
        }

        $book->clearAuthors();
        $book->clearSubjects();

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        $this->entityManager->commit();
    }
}
