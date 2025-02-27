<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\DatabaseConnectionException;
use App\Exception\DuplicateEntryException;
use App\Exception\NoRecordsFoundException;
use App\Exception\UnexpectedApplicationException;
use App\Service\AuthorService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/authors', name: 'app_api_author_')]
final class AuthorController extends AbstractController
{
    public function __construct(
        protected readonly AuthorService $authorService,
        protected readonly LoggerInterface $logger,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['message' => 'Nome é obrigatória'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $author = $this->authorService->createAuthor($data['name']);
            return new JsonResponse([
                'id' => $author->getId(),
                'name' => $author->getName()
            ], Response::HTTP_CREATED);
        } catch (DuplicateEntryException $e) {
            $this->logger->error('Erro de chave única: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (DatabaseConnectionException $e) {
            $this->logger->error('Erro de conexão com o banco: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UnexpectedApplicationException $e) {
            $this->logger->error('Erro inesperado: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'fetch', methods: ['GET'])]
    public function fetchSubjects(): JsonResponse
    {
        try {
            $subjects = $this->authorService->getAllAuthors();
            return new JsonResponse($subjects, Response::HTTP_OK);
        } catch (NoRecordsFoundException $e) {
            $this->logger->error('Erro ao buscar assuntos: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['message' => 'JSON Inválido'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedAuthor = $this->authorService->updateAuthor($id, $data);

            return new JsonResponse([
                'id' => $updatedAuthor->getId(),
                'description' => $updatedAuthor->getName()
            ], Response::HTTP_OK);

        } catch (\Throwable $e) {
            $this->logger->error('Erro ao atualizar assunto: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->authorService->deleteAuthor($id);
            return $this->json(['message' => 'Registro excluído com sucesso'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erro ao excluir registro'], Response::HTTP_BAD_REQUEST);
        }
    }
}
