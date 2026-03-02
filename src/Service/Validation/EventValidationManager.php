<?php

namespace App\Service\Validation;

use App\Entity\Event;

class EventValidationManager
{
    /**
     * @param Event $event
     * @return string[]
     */
    public function validate(Event $event): array
    {
        $errors = [];

        if (empty($event->getTitre())) {
            $errors[] = "Le titre est obligatoire.";
        }

        if ($event->getDateDebut() && $event->getDateFin()) {
            if ($event->getDateFin() <= $event->getDateDebut()) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
        }

        if ($event->getCapacite() <= 0) {
            $errors[] = "La capacité doit être supérieure à zéro.";
        }

        return $errors;
    }

    public function isValid(Event $event): bool
    {
        return count($this->validate($event)) === 0;
    }
}
