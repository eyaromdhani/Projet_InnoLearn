<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserManager;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    public function testValidUser()
    {
        $user = new User();
        $user->setName('Jean Dupont');
        $user->setEmail('jean.dupont@example.com');

        $manager = new UserManager();
        $this->assertTrue($manager->validate($user));
    }

    public function testUserShortName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom d'utilisateur doit contenir au moins 3 caractères.");

        $user = new User();
        $user->setName('Jo');
        $user->setEmail('jo@example.com');

        $manager = new UserManager();
        $manager->validate($user);
    }

    public function testUserInvalidEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("L'adresse email est invalide.");

        $user = new User();
        $user->setName('Test User');
        $user->setEmail('invalid-email');

        $manager = new UserManager();
        $manager->validate($user);
    }
}
