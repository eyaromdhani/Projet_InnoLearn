<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidName extends Constraint
{
    public string $message = 'This name looks like nonsense. Please enter your real name.';
}
