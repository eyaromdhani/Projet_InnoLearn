<?php

namespace App\Service;

use App\Entity\OffreStage;

class OffreStageManager
{
    /**
     * Validates an OffreStage entity based on business rules.
     */
    public function validate(OffreStage $offre): bool
    {
        if (empty($offre->getTitre())) {
            throw new \InvalidArgumentException("Le titre de l'offre est obligatoire.");
        }

        if (empty($offre->getEntreprise())) {
            throw new \InvalidArgumentException("Le nom de l'entreprise est obligatoire.");
        }

        if ($offre->getDuree() <= 0) {
            throw new \InvalidArgumentException("La durée du stage doit être supérieure à zéro.");
        }

        return true;
    }
}
