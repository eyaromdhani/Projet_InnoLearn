<?php

namespace App\Service\Validation;

use App\Entity\Quiz;

class QuizValidationManager
{
    /**
     * @param Quiz $quiz
     * @return string[]
     */
    public function validate(Quiz $quiz): array
    {
        $errors = [];

        if (empty($quiz->getTitle())) {
            $errors[] = "Le titre du quiz est obligatoire.";
        }

        return $errors;
    }

    public function isValid(Quiz $quiz): bool
    {
        return count($this->validate($quiz)) === 0;
    }
}
