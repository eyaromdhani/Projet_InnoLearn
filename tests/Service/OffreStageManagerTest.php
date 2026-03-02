<?php

namespace App\Tests\Service;

use App\Entity\OffreStage;
use App\Service\OffreStageManager;
use PHPUnit\Framework\TestCase;

class OffreStageManagerTest extends TestCase
{
    public function testValidOffre()
    {
        $offre = new OffreStage();
        $offre->setTitre('Stage Développeur Symfony');
        $offre->setEntreprise('InnoLearn Tech');
        $offre->setDuree(6);

        $manager = new OffreStageManager();
        $this->assertTrue($manager->validate($offre));
    }

    public function testOffrePositiveDuration()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("La durée du stage doit être supérieure à zéro.");

        $offre = new OffreStage();
        $offre->setTitre('Test Duration');
        $offre->setEntreprise('Test Ent');
        $offre->setDuree(-1);

        $manager = new OffreStageManager();
        $manager->validate($offre);
    }
}
