<?php

namespace App\Service;

use App\Entity\User;

class UserManager
{
    /**
     * Validates a User entity based on business rules.
     * 1. Name must be at least 3 characters long.
     * 2. Email must be valid (already partially handled by entity, but we add logic here).
     */
    public function validate(User $user): bool
    {
        if (empty($user->getName()) || strlen($user->getName()) < 3) {
            throw new \InvalidArgumentException("Le nom d'utilisateur doit contenir au moins 3 caractères.");
        }

        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'adresse email est invalide.");
        }

        return true;
    }
}
