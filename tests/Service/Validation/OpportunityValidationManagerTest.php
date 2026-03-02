<?php

namespace App\Tests\Service\Validation;

use App\Entity\OffreStage;
use App\Service\Validation\OpportunityValidationManager;
use PHPUnit\Framework\TestCase;

class OpportunityValidationManagerTest extends TestCase
{
    private OpportunityValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new OpportunityValidationManager();
    }

    public function testValidateWithValidOpportunity(): void
    {
        $offre = new OffreStage();
        $offre->setTitre('Stage Symfony');
        $offre->setDescription('Un super stage de 6 mois.');
        $offre->setStatut('ouverte');

        $errors = $this->manager->validate($offre);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($offre));
    }

    public function testValidateWithInvalidStatus(): void
    {
        $offre = new OffreStage();
        $offre->setTitre('Stage PHP');
        $offre->setStatut('invalid');

        $errors = $this->manager->validate($offre);
        $this->assertContains("Le statut n'est pas valide.", $errors);
    }
}
