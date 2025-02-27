<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\AuthorNotFoundException;
use App\Exception\BookUpdateException;
use App\Exception\DatabaseConnectionException;
use App\Exception\DataRetrievalException;
use App\Exception\DuplicateEntryException;
use App\Exception\EntityNotFoundException;
use App\Exception\EntityOperationException;
use App\Exception\NoRecordsFoundException;
use App\Exception\SubjectNotFoundException;
use App\Exception\UnexpectedApplicationException;
use App\Service\AuthorService;
use App\Service\BookService;
use App\Service\SubjectService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/books', name: 'app_api_book_')]
final class BookController extends AbstractController
{
    public function __construct(
        protected readonly BookService $bookService,
        protected readonly SubjectService $subjectService,
        protected readonly AuthorService $authorService,
        protected readonly LoggerInterface $logger)
    {
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $book = $this->bookService->createBook($data);
            return new JsonResponse([
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ], Response::HTTP_CREATED);
        } catch (DuplicateEntryException $e) {
            $this->logger->error('Erro de chave única: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (EntityNotFoundException $e) {
            $this->logger->warning('Entidade não encontrada: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (DatabaseConnectionException $e) {
            $this->logger->error('Erro de conexão com o banco: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UnexpectedApplicationException $e) {
            $this->logger->error('Erro inesperado: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['message' => 'JSON Inválido'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updateBook = $this->bookService->updateBook($id, $data);

            return new JsonResponse([
                'id' => $updateBook->getId(),
                'title' => $updateBook->getTitle(),
                'publisher' => $updateBook->getPublisher(),
                'edition' => $updateBook->getEdition(),
                'publicationYear' => $updateBook->getPublicationYear(),
                'price' => $updateBook->getPrice(),
            ], Response::HTTP_OK);

        } catch (BookUpdateException $e) {
            $this->logger->error('Erro ao atualizar livro: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (DuplicateEntryException $e) {
            $this->logger->error('Erro ao atualizar livro: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/authors', name: 'load_authors', methods: ['GET'])]
    public function getAuthors(): JsonResponse
    {
        try {
            $authors = $this->authorService->getAllAuthors();
            return $this->json($authors, Response::HTTP_OK);
        } catch (AuthorNotFoundException $e) {
            $this->logger->warning('Nenhum autor encontrado: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (DataRetrievalException | DatabaseConnectionException $e) {
            $this->logger->error('Erro ao recuperar autores: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->critical('Erro inesperado ao buscar autores: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['error' => 'Erro interno ao buscar autores'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/subjects', name: 'load_subjects', methods: ['GET'])]
    public function getSubjects(): JsonResponse
    {
        try {
            $subjects = $this->subjectService->getAllSubjects();
            return $this->json($subjects, Response::HTTP_OK);
        } catch (SubjectNotFoundException $e) {
            $this->logger->warning('Nenhum assunto encontrado: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (DataRetrievalException | DatabaseConnectionException $e) {
            $this->logger->error('Erro ao recuperar assuntos: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UnexpectedApplicationException $e) {
            $this->logger->critical('Erro inesperado ao buscar assuntos: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['error' => 'Erro interno ao buscar assuntos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/fetch', name: 'fetch', methods: ['GET'])]
    public function fetchBooks(): JsonResponse
    {
        try {
            $books = $this->bookService->getAllBooks();
            return new JsonResponse($books, Response::HTTP_OK);
        } catch (NoRecordsFoundException $e) {
            $this->logger->error('Erro ao buscar livros: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->bookService->deleteBook($id);
            return new JsonResponse(['message' => 'Livro deletado com sucesso'], Response::HTTP_OK);
        } catch (EntityNotFoundException $e) {
            $this->logger->error('Livro não encontrado: ' . $e->getMessage(), ['exception' => $e], );
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (EntityOperationException $e) {
            $this->logger->error('Erro ao deletar livro: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => 'Erro interno ao tentar excluir o livro'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
