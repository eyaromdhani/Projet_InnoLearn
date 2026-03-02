<?php

namespace App\Service\Validation;

use App\Entity\User;

class UserValidationManager
{
    /**
     * @param User $user
     * @return string[]
     */
    public function validate(User $user): array
    {
        $errors = [];

        if (empty($user->getEmail())) {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        }

        if (empty($user->getName())) {
            $errors[] = "Le nom est obligatoire.";
        }

        if (count($user->getRoles()) === 0) {
            $errors[] = "Au moins un rôle est requis.";
        }

        return $errors;
    }

    public function isValid(User $user): bool
    {
        return count($this->validate($user)) === 0;
    }
}
