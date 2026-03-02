<?php

namespace App\Service\Validation;

use App\Entity\CategorieCours;

class CategorieCoursValidationManager
{
    /**
     * @param CategorieCours $category
     * @return string[]
     */
    public function validate(CategorieCours $category): array
    {
        $errors = [];

        if (empty($category->getTitre())) {
            $errors[] = "Le titre est obligatoire.";
        }

        if (empty($category->getDescription())) {
            $errors[] = "La description est obligatoire.";
        }

        return $errors;
    }

    public function isValid(CategorieCours $category): bool
    {
        return count($this->validate($category)) === 0;
    }
}
