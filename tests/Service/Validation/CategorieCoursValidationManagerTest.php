<?php

namespace App\Tests\Service\Validation;

use App\Entity\CategorieCours;
use App\Service\Validation\CategorieCoursValidationManager;
use PHPUnit\Framework\TestCase;

class CategorieCoursValidationManagerTest extends TestCase
{
    private CategorieCoursValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new CategorieCoursValidationManager();
    }

    public function testValidateWithValidCategory(): void
    {
        $cat = new CategorieCours();
        $cat->setTitre('Development');
        $cat->setDescription('Learn to code.');

        $errors = $this->manager->validate($cat);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($cat));
    }
}
