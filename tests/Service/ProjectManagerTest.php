<?php

namespace App\Tests\Service;

use App\Entity\Project;
use App\Service\ProjectManager;
use PHPUnit\Framework\TestCase;

class ProjectManagerTest extends TestCase
{
    public function testValidProject()
    {
        $project = new Project();
        $project->setTitle('Plateforme E-learning');
        $project->setStatus('active');
        $project->setStartDate(new \DateTime('2026-01-01'));
        $project->setEndDate(new \DateTime('2026-12-31'));

        $manager = new ProjectManager();
        $this->assertTrue($manager->validate($project));
    }

    public function testProjectWithoutTitle()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre du projet est obligatoire.");

        $project = new Project();
        $project->setStatus('draft');

        $manager = new ProjectManager();
        $manager->validate($project);
    }

    public function testProjectInvalidStatus()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le statut du projet est invalide.");

        $project = new Project();
        $project->setTitle('Test Status');
        $project->setStatus('unknown_status');

        $manager = new ProjectManager();
        $manager->validate($project);
    }
}
