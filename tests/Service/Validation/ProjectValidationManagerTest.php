<?php

namespace App\Tests\Service\Validation;

use App\Entity\Project;
use App\Service\Validation\ProjectValidationManager;
use PHPUnit\Framework\TestCase;

class ProjectValidationManagerTest extends TestCase
{
    private ProjectValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new ProjectValidationManager();
    }

    public function testValidateWithValidProject(): void
    {
        $project = new Project();
        $project->setTitle('Projet InnoLearn');
        $project->setDescription('Plateforme e-learning innovante.');
        $project->setStartDate(new \DateTime('now'));
        $project->setEndDate(new \DateTime('+1 month'));

        $errors = $this->manager->validate($project);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($project));
    }

    public function testValidateWithInvalidDates(): void
    {
        $project = new Project();
        $project->setTitle('Projet Test');
        $project->setStartDate(new \DateTime('+1 month'));
        $project->setEndDate(new \DateTime('now'));

        $errors = $this->manager->validate($project);
        $this->assertContains("La date de fin doit être après la date de début.", $errors);
    }
}
