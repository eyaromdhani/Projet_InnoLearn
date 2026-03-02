<?php

namespace App\Service;

use App\Entity\Project;

class ProjectManager
{
    /**
     * Validates a Project entity based on business rules.
     */
    public function validate(Project $project): bool
    {
        if (empty($project->getTitle())) {
            throw new \InvalidArgumentException("Le titre du projet est obligatoire.");
        }

        if ($project->getStartDate() && $project->getEndDate()) {
            if ($project->getEndDate() <= $project->getStartDate()) {
                throw new \InvalidArgumentException("La date de fin doit être après la date de début.");
            }
        }

        $allowedStatuses = ['draft', 'active', 'completed', 'cancelled'];
        if (!in_array($project->getStatus(), $allowedStatuses)) {
            throw new \InvalidArgumentException("Le statut du projet est invalide.");
        }

        return true;
    }
}
