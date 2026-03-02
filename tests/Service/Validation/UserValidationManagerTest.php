<?php

namespace App\Tests\Service\Validation;

use App\Entity\User;
use App\Service\Validation\UserValidationManager;
use PHPUnit\Framework\TestCase;

class UserValidationManagerTest extends TestCase
{
    private UserValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new UserValidationManager();
    }

    public function testValidateWithValidUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setRoles(['ROLE_USER']);

        $errors = $this->manager->validate($user);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($user));
    }

    public function testValidateWithInValidEmail(): void
    {
        $user = new User();
        $user->setEmail('invalid-email');
        $user->setName('Test User');
        $user->setRoles(['ROLE_USER']);

        $errors = $this->manager->validate($user);
        $this->assertContains("L'email n'est pas valide.", $errors);
    }

    public function testValidateWithEmptyFields(): void
    {
        $user = new User();
        $errors = $this->manager->validate($user);
        $this->assertGreaterThan(0, count($errors));
    }
}
