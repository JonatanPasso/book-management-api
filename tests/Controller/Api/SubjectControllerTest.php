<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\SubjectController;
use App\Entity\Subject;
use App\Service\SubjectService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SubjectControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateSubject(): void
    {
        /** @var SubjectService|MockObject $subjectServiceMock */
        $subjectServiceMock = $this->createMock(SubjectService::class);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        // Instância simulada do Subject que será retornada pelo SubjectService
        $mockedSubject = new Subject();
        $mockedSubject->setId(1); // Supondo que existe um setter para `id`
        $mockedSubject->setDescription('Matemática'); // Supondo que existe um setter para `description`

        // Configuração do Mock para o método createSubject
        $subjectServiceMock->expects($this->once())
            ->method('createSubject')
            ->with('Matemática')
            ->willReturn($mockedSubject);

        // Instância do controller com os mocks
        $subjectController = new SubjectController($subjectServiceMock, $loggerMock);

        // Simulação de uma requisição com um body JSON válido
        $request = new Request([], [], [], [], [], [], json_encode(['description' => 'Matemática']));

        // Chamada ao método `create`
        $response = $subjectController->create($request);

        // Verifica se o tipo da resposta está correto
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Verifica se o código de status HTTP é 201 (Created)
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        // Verifica se o conteúdo retornado é o esperado
        $this->assertJsonStringEqualsJsonString(
            json_encode(['id' => 1, 'description' => 'Matemática']),
            $response->getContent()
        );
    }
}