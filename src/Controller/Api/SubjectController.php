<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\DatabaseConnectionException;
use App\Exception\DuplicateEntryException;
use App\Exception\EntityOperationException;
use App\Exception\NoRecordsFoundException;
use App\Exception\UnexpectedApplicationException;
use App\Service\SubjectService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/subjects', name: 'app_api_subject_')]
final class SubjectController extends AbstractController
{
    public function __construct(
        protected readonly SubjectService $subjectService,
        protected readonly LoggerInterface $logger,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['description'])) {
            return $this->json(['message' => 'A descrição é obrigatória'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $subject = $this->subjectService->createSubject($data['description']);
            return new JsonResponse([
                'id' => $subject->getId(),
                'description' => $subject->getDescription()
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
            $subjects = $this->subjectService->getAllSubjects();
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
            $updatedSubject = $this->subjectService->updateSubject($id, $data);

            return new JsonResponse([
                'id' => $updatedSubject->getId(),
                'description' => $updatedSubject->getDescription()
            ], Response::HTTP_OK);

        } catch (EntityOperationException $e) {
            $this->logger->error('Erro ao atualizar assunto: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $this->subjectService->deleteSubject($id);
            return $this->json(['message' => 'Registro excluído com sucesso'], Response::HTTP_OK);
        } catch (EntityOperationException $e) {
            $this->logger->error('Erro ao excluir registro: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['error' => 'Erro ao excluir registro'], Response::HTTP_BAD_REQUEST);
        } catch (NoRecordsFoundException $e) {
            $this->logger->error('Nenhum registro encontrado.: ' . $e->getMessage(), ['exception' => $e]);
            return $this->json(['error' => 'Nenhum registro encontrado.'], Response::HTTP_NOT_FOUND);
        }
    }
}
