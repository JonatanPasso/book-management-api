<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class, readOnly: true)]
#[ORM\Table(name: "vw_books_by_author_and_subject")]
class Report
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name: "authorId", type: "integer")]
    private int $authorId;

    #[ORM\Column(name: "authorName", type: "string")]
    private string $authorName;

    #[ORM\Column(name: "subjectId", type: "integer")]
    private int $subjectId;

    #[ORM\Column(name: "subjectName", type: "string")]
    private string $subjectName;

    #[ORM\Column(name: "bookId", type: "integer")]
    private int $bookId;

    #[ORM\Column(name: "bookTitle", type: "string")]
    private string $bookTitle;

    #[ORM\Column(name: "bookPublisher", type: "string")]
    private string $bookPublisher;

    #[ORM\Column(name: "bookEdition", type: "integer")]
    private int $bookEdition;

    #[ORM\Column(name: "yearPublished", type: "integer", nullable: true)]
    private ?int $yearPublished;

    #[ORM\Column(name: "bookPrice", type: "float")]
    private float $bookPrice;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getSubjectId(): int
    {
        return $this->subjectId;
    }

    public function getSubjectName(): string
    {
        return $this->subjectName;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function getBookPublisher(): string
    {
        return $this->bookPublisher;
    }

    public function getBookEdition(): int
    {
        return $this->bookEdition;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }

    public function getBookPrice(): float
    {
        return $this->bookPrice;
    }
}
