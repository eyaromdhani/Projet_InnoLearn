<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Specifically block ROLE_ADMIN on the frontend login BEFORE password check for better performance
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
