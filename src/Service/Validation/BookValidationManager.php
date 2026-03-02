<?php

namespace App\Service\Validation;

use App\Entity\Book;

class BookValidationManager
{
    /**
     * @param Book $book
     * @return string[]
     */
    public function validate(Book $book): array
    {
        $errors = [];

        if (empty($book->getTitre())) {
            $errors[] = "Le titre est obligatoire.";
        }

        if (empty($book->getAuthor())) {
            $errors[] = "L'auteur est obligatoire.";
        }

        if (empty($book->getDescription())) {
            $errors[] = "La description est obligatoire.";
        }

        return $errors;
    }

    public function isValid(Book $book): bool
    {
        return count($this->validate($book)) === 0;
    }
}
