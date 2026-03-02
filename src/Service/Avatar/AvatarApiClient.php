<?php

namespace App\Service\Avatar;

/**
 * Placeholder for AvatarApiClient if it was expected by other services.
 */
class AvatarApiClient
{
    public function generateAvatar(array $params)
    {
        // Placeholder implementation
        return null;
    }

    public function createAvatar(\App\Entity\User $user, array $params)
    {
        return null;
    }

    public function createAvatarFromDescription(\App\Entity\User $user, string $description)
    {
        return null;
    }
}
