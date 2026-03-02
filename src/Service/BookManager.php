<?php

namespace App\Service;

use App\Entity\Book;

class BookManager
{
    /**
     * Validates a Book entity based on business rules.
     * 1. Title is required.
     * 2. Author field must not be empty if provided.
     */
    public function validate(Book $book): bool
    {
        if (empty($book->getTitre())) {
            throw new \InvalidArgumentException("Le titre du livre est obligatoire.");
        }

        if (empty($book->getAuthor())) {
            throw new \InvalidArgumentException("L'auteur du livre est obligatoire.");
        }

        return true;
    }
}
