<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class UserChangeListener
{
    /**
     * Invalidate MFA verification key when password or phone number changes
     */
    public function preUpdate(User $user, PreUpdateEventArgs $args): void
    {
        // Check if password_hash or phone_number or country_code changed
        if ($args->hasChangedField('password') || 
            $args->hasChangedField('phoneNumber') || 
            $args->hasChangedField('countryCode')) {
            
            // Invalidate the verification key by setting it to null
            $user->setVerificationKey(null);
            $user->setKeyExpiresAt(null);
        }
    }
}
