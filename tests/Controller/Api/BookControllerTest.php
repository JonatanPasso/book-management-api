<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\BookController;
use App\Entity\Book;
use App\Service\BookService;
use App\Service\SubjectService;
use App\Service\AuthorService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BookControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateBook(): void
    {
        /** @var BookService|MockObject $bookServiceMock */
        $bookServiceMock = $this->createMock(BookService::class);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        /** @var SubjectService|MockObject $subjectServiceMock */
        $subjectServiceMock = $this->createMock(SubjectService::class);

        /** @var AuthorService|MockObject $authorServiceMock */
        $authorServiceMock = $this->createMock(AuthorService::class);

        $mockedBook = new Book();
        $mockedBook->setId(1);
        $mockedBook->setTitle('Livro de Teste');

        $bookServiceMock->expects($this->once())
            ->method('createBook')
            ->with(['title' => 'Livro de Teste'])
            ->willReturn($mockedBook);

        $bookController = new BookController(
            $bookServiceMock,
            $subjectServiceMock,
            $authorServiceMock,
            $loggerMock
        );

        $request = new Request([], [], [], [], [], [], json_encode(['title' => 'Livro de Teste']));

        $response = $bookController->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['id' => 1, 'title' => 'Livro de Teste']),
            $response->getContent()
        );
    }
}