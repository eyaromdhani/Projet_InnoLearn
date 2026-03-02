<?php

namespace App\Tests\Service;

use App\Entity\Event;
use App\Service\EventManager;
use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{
    public function testValidEvent()
    {
        $event = new Event();
        $event->setTitre('Conférence Symfony');
        $event->setDateDebut(new \DateTime('2026-06-01 10:00:00'));
        $event->setDateFin(new \DateTime('2026-06-01 12:00:00'));

        $manager = new EventManager();
        $this->assertTrue($manager->validate($event));
    }

    public function testEventWithoutTitle()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le titre de l'événement est obligatoire.");

        $event = new Event();
        $event->setDateDebut(new \DateTime('2026-06-01 10:00:00'));

        $manager = new EventManager();
        $manager->validate($event);
    }

    public function testEventInvalidDateRange()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("La date de fin doit être postérieure à la date de début.");

        $event = new Event();
        $event->setTitre('Test Dates');
        $event->setDateDebut(new \DateTime('2026-06-01 12:00:00'));
        $event->setDateFin(new \DateTime('2026-06-01 10:00:00'));

        $manager = new EventManager();
        $manager->validate($event);
    }
}
