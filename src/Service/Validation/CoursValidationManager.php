<?php

namespace App\Service\Validation;

use App\Entity\Cours;

class CoursValidationManager
{
    /**
     * @param Cours $cours
     * @return string[]
     */
    public function validate(Cours $cours): array
    {
        $errors = [];

        if (empty($cours->getNom())) {
            $errors[] = "Le nom du cours est obligatoire.";
        }

        if (empty($cours->getDescription())) {
            $errors[] = "La description est obligatoire.";
        }

        if ($cours->getDuree() < 0) {
            $errors[] = "La durée ne peut pas être négative.";
        }

        return $errors;
    }

    public function isValid(Cours $cours): bool
    {
        return count($this->validate($cours)) === 0;
    }
}
