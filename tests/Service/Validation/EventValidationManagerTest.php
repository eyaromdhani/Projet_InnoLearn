<?php

namespace App\Tests\Service\Validation;

use App\Entity\Event;
use App\Service\Validation\EventValidationManager;
use PHPUnit\Framework\TestCase;

class EventValidationManagerTest extends TestCase
{
    private EventValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new EventValidationManager();
    }

    public function testValidateWithValidEvent(): void
    {
        $event = new Event();
        $event->setTitre('Workshop PHP');
        $event->setDateDebut(new \DateTime('now'));
        $event->setDateFin(new \DateTime('+2 hours'));
        $event->setCapacite(50);

        $errors = $this->manager->validate($event);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($event));
    }

    public function testValidateWithInvalidDates(): void
    {
        $event = new Event();
        $event->setTitre('Workshop PHP');
        $event->setDateDebut(new \DateTime('+2 hours'));
        $event->setDateFin(new \DateTime('now'));
        $event->setCapacite(50);

        $errors = $this->manager->validate($event);
        $this->assertContains("La date de fin doit être après la date de début.", $errors);
    }

    public function testValidateWithInvalidCapacity(): void
    {
        $event = new Event();
        $event->setTitre('Workshop PHP');
        $event->setCapacite(0);

        $errors = $this->manager->validate($event);
        $this->assertContains("La capacité doit être supérieure à zéro.", $errors);
    }
}
