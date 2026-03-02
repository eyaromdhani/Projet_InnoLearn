<?php

namespace App\Service\Validation;

use App\Entity\Project;

class ProjectValidationManager
{
    /**
     * @param Project $project
     * @return string[]
     */
    public function validate(Project $project): array
    {
        $errors = [];

        if (empty($project->getTitle())) {
            $errors[] = "Le titre est obligatoire.";
        }

        if (empty($project->getDescription())) {
            $errors[] = "La description est obligatoire.";
        }

        if ($project->getStartDate() && $project->getEndDate()) {
            if ($project->getEndDate() <= $project->getStartDate()) {
                $errors[] = "La date de fin doit être après la date de début.";
            }
        }

        return $errors;
    }

    public function isValid(Project $project): bool
    {
        return count($this->validate($project)) === 0;
    }
}
