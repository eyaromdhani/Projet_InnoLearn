<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof ValidName) {
            return;
        }

        // Repetitive character check (e.g., "aaaaa")
        if (preg_match('/(.)\1{3,}/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
            return;
        }

        // Vowel check: Real names usually have at least one vowel
        $vowels = preg_match_all('/[aeiouy]/i', $value);
        if ($vowels === 0 && strlen($value) > 3) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
