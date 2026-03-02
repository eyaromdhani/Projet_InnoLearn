<?php

namespace App\Service\Validation;

use App\Entity\OffreStage;

class OpportunityValidationManager
{
    /**
     * @param OffreStage $offre
     * @return string[]
     */
    public function validate(OffreStage $offre): array
    {
        $errors = [];

        if (empty($offre->getTitre())) {
            $errors[] = "Le titre est obligatoire.";
        }

        if (empty($offre->getDescription())) {
            $errors[] = "La description est obligatoire.";
        }

        $allowedStatuses = ['ouverte', 'fermée'];
        if (!in_array($offre->getStatut(), $allowedStatuses)) {
            $errors[] = "Le statut n'est pas valide.";
        }

        return $errors;
    }

    public function isValid(OffreStage $offre): bool
    {
        return count($this->validate($offre)) === 0;
    }
}
