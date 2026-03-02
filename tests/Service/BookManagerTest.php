<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Service\BookManager;
use PHPUnit\Framework\TestCase;

class BookManagerTest extends TestCase
{
    public function testValidBook()
    {
        $book = new Book();
        $book->setTitre('Le Petit Prince');
        $book->setAuthor('Antoine de Saint-Exupéry');

        $manager = new BookManager();
        $this->assertTrue($manager->validate($book));
    }

    public function testBookWithoutTitle()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre du livre est obligatoire.");

        $book = new Book();
        $book->setAuthor('Auteur Inconnu');

        $manager = new BookManager();
        $manager->validate($book);
    }

    public function testBookWithoutAuthor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("L'auteur du livre est obligatoire.");

        $book = new Book();
        $book->setTitre('Titre Inconnu');

        $manager = new BookManager();
        $manager->validate($book);
    }
}
