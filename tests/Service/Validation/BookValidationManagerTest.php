<?php

namespace App\Tests\Service\Validation;

use App\Entity\Book;
use App\Service\Validation\BookValidationManager;
use PHPUnit\Framework\TestCase;

class BookValidationManagerTest extends TestCase
{
    private BookValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new BookValidationManager();
    }

    public function testValidateWithValidBook(): void
    {
        $book = new Book();
        $book->setTitre('PHP Mastery');
        $book->setAuthor('John Doe');
        $book->setDescription('A great book about PHP.');

        $errors = $this->manager->validate($book);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($book));
    }

    public function testValidateWithMissingFields(): void
    {
        $book = new Book();
        $errors = $this->manager->validate($book);
        $this->assertCount(3, $errors);
    }
}
