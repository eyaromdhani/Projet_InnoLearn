<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

#[AsEventListener(event: InteractiveLoginEvent::class)]
class AdminLoginSuccessListener
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(InteractiveLoginEvent $event): void
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        // Only process admin users
        if (!$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        // Clear verification flag on fresh auth
        $session->remove('admin_access_verified');

        // If enrolled admin, redirect to verify-access
        if ($user->requiresAdminStrongVerification()) {
            $session->set('admin_access_target', $this->urlGenerator->generate('admin_dashboard'));
            $request->attributes->set('admin_needs_verification', true);
        }
    }
}
