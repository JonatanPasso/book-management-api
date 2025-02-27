<?php

namespace App\Controller\Api;

use App\Service\ReportAuthorService;
use App\Service\ReportBookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/api/report', name: 'app_api_report_')]
final class ReportController extends AbstractController
{
    public function __construct(
        protected readonly ReportAuthorService $reportAuthorService,
        protected readonly ReportBookService $reportBookService,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/author', name: 'report_by_author', methods: ['GET'])]
    public function reportByAuthor(Request $request): Response
    {
        return $this->reportAuthorService->generateReportByAuthor($request);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/book', name: 'report_by_book', methods: ['GET'])]
    public function reportByBook(Request $request): Response
    {
        return $this->reportBookService->generateReportByBook($request);
    }
}
