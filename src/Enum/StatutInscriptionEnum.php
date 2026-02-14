<?php

namespace App\Enum;

enum StatutInscriptionEnum: string
{
    case EN_ATTENTE = 'S\'inscrire';
    case CONFIRMEE = 'Confirmée';
    case ANNULEE = 'Annulée';
}
