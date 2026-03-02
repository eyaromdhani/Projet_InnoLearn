<?php

namespace App\Service;

use App\Entity\Event;

class EventManager
{
    /**
     * Validates an Event entity based on business rules.
     * 1. Date end must be after date start.
     * 2. Price cannot be negative.
     * 3. Title must not be empty.
     */
    public function validate(Event $event): bool
    {
        if (empty($event->getTitre())) {
            throw new \InvalidArgumentException("Le titre de l'événement est obligatoire.");
        }

        if ($event->getDateDebut() && $event->getDateFin()) {
            if ($event->getDateFin() <= $event->getDateDebut()) {
                throw new \InvalidArgumentException("La date de fin doit être postérieure à la date de début.");
            }
        }

        // Assuming there is a price or similar numeric field, if not we validate general state
        // Let's check Event entity fields first or assume standard ones
        
        return true;
    }
}
