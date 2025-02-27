<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\AuthorController;
use App\Entity\Author;
use App\Service\AuthorService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateAuthor(): void
    {
        /** @var AuthorService|MockObject $authorServiceMock */
        $authorServiceMock = $this->createMock(AuthorService::class);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $mockedAuthor = new Author();
        $mockedAuthor->setId(1);
        $mockedAuthor->setName('John Doe');

        $authorServiceMock->expects($this->once())
        ->method('createAuthor')
            ->with('John Doe')
            ->willReturn($mockedAuthor);

        $authorController = new AuthorController($authorServiceMock, $loggerMock);

        $request = new Request([], [], [], [], [], [], json_encode(['name' => 'John Doe']));

        // Chama o método `create`
        $response = $authorController->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['id' => 1, 'name' => 'John Doe']),
            $response->getContent()
        );
    }
}