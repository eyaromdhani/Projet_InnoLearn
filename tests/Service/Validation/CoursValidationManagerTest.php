<?php

namespace App\Tests\Service\Validation;

use App\Entity\Cours;
use App\Service\Validation\CoursValidationManager;
use PHPUnit\Framework\TestCase;

class CoursValidationManagerTest extends TestCase
{
    private CoursValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new CoursValidationManager();
    }

    public function testValidateWithValidCours(): void
    {
        $cours = new Cours();
        $cours->setNom('PHP Basics');
        $cours->setDescription('Introduction to PHP.');
        $cours->setDuree(10);

        $errors = $this->manager->validate($cours);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($cours));
    }

    public function testValidateWithInvalidDuration(): void
    {
        $cours = new Cours();
        $cours->setNom('PHP Basics');
        $cours->setDuree(-5);

        $errors = $this->manager->validate($cours);
        $this->assertContains("La durée ne peut pas être négative.", $errors);
    }
}
