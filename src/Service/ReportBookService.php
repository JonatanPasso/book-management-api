<?php

declare(strict_types=1);

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\Repository\ReportRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ReportBookService
{
    public function __construct(
        protected Pdf $pdf,
        protected Environment $twig,
        protected ReportRepository $bookByAuthorAndSubjectRepository
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function generateReportByBook(Request $request): Response
    {
        $books = $this->bookByAuthorAndSubjectRepository->findAllGroupedByAuthor();

        $html = $this->twig->render('report/book.html.twig', [
            'books' => $books
        ]);

        // Verifique se o parâmetro "download" está presente na URL
        $isDownload = $request->query->get('download', false);

        $pdfContent = $this->pdf->getOutputFromHtml($html, [
            'orientation' => 'Landscape',
            'page-size'   => 'A4',
            'margin-top'    => 10,
            'margin-right'  => 10,
            'margin-bottom' => 10,
            'margin-left'   => 10,
            'disable-smart-shrinking' => false,
            'title' => 'Relatório de Livros por Autor e Assunto'
        ]);

        // Se o parâmetro "download" estiver presente, force o download
        if ($isDownload) {
            return new Response(
                $pdfContent,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="relatorio_por_livro.pdf"' // Forçar o download
                ]
            );
        }

        // Caso contrário, exiba inline
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio_por_autor.pdf"' // Visualizar inline
            ]
        );
    }
}
