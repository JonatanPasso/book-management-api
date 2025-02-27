<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227035709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criação da VIEW vw_books_by_author_and_subject';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_books_by_author_and_subject');

        $this->addSql(<<<SQL
            CREATE VIEW vw_books_by_author_and_subject AS
            SELECT 
                ROW_NUMBER() OVER() AS id,
                a.CodAu AS authorId,
                a.Nome AS authorName,
                s.CodAs AS subjectId,
                s.Descricao AS subjectName,
                l.Codl AS bookId,
                l.Titulo AS bookTitle,
                l.Editora AS bookPublisher,
                l.Edicao AS bookEdition,
                l.AnoPublicacao AS yearPublished,
                l.Preco AS bookPrice,
                GROUP_CONCAT(DISTINCT s.Descricao ORDER BY s.Descricao SEPARATOR ', ') AS bookSubjects
            FROM Autor a
            JOIN Livro_Autor la ON a.CodAu = la.Autor_CodAu
            JOIN Livro l ON la.Livro_Codl = l.Codl
            LEFT JOIN Livro_Assunto la2 ON l.Codl = la2.Livro_Codl
            LEFT JOIN Assunto s ON la2.Assunto_codAs = s.CodAs
            GROUP BY 
                a.CodAu, a.Nome, 
                s.CodAs, s.Descricao, 
                l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao;
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS vw_books_by_author_and_subject');
    }
}
