<?php

namespace App\Enum;

enum StatutEvenementEnum: string
{
    case ACTIF = 'actif';
    case ANNULE = 'annule';
    case TERMINE = 'termine';
}
