<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: 'Livro', indexes: [
    new ORM\Index(name: "Livro_Autor_FkIndex1", columns: ["Livro_Codl"]),
    new ORM\Index(name: "Livro_Autor_FkIndex2", columns: ["Autor_CodAu"]),
    new ORM\Index(name: "Livro_Assunto_FKIndex1", columns: ["Livro_Codl"]),
    new ORM\Index(name: "Livro_Assunto_FKIndex2", columns: ["Assunto_CodAs"])
])]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Codl', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'Titulo', type: 'string', length: 40)]
    private string $title;

    #[ORM\Column(name: 'Editora', type: 'string', length: 40)]
    private string $publisher;

    #[ORM\Column(name: 'Edicao', type: 'integer')]
    private int $edition;

    #[ORM\Column(name: 'AnoPublicacao', type: 'string', length: 4)]
    private string $publicationYear;

    #[ORM\Column(name: 'Preco', type: 'float')]
    private float $price;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'Livro_Autor',
        joinColumns: [new ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'Autor_CodAu', referencedColumnName: 'CodAu')]
    )]
    private Collection $authors;

    /**
     * @var Collection<int, Subject>
     */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'Livro_Assunto',
        joinColumns: [new ORM\JoinColumn(name: 'Livro_Codl', referencedColumnName: 'Codl')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'Assunto_CodAs', referencedColumnName: 'CodAs')]
    )]
    private Collection $subjects;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->subjects = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Book
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): static
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getEdition(): int
    {
        return $this->edition;
    }

    public function setEdition(int $edition): static
    {
        $this->edition = $edition;
        return $this;
    }

    public function getPublicationYear(): string
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(string $publicationYear): static
    {
        $this->publicationYear = $publicationYear;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        if ($this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }
        return $this;
    }

    public function clearAuthors(): void
    {
        foreach ($this->authors as $author) {
            $this->removeAuthor($author);
        }
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }
        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->contains($subject)) {
            $this->subjects->removeElement($subject);
        }
        return $this;
    }

    public function clearSubjects(): void
    {
        foreach ($this->subjects as $subject) {
            $this->removeSubject($subject);
        }
    }
}
