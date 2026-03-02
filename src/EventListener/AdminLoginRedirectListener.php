<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Security;

#[AsEventListener(event: RequestEvent::class, method: 'onKernelRequest')]
class AdminLoginRedirectListener
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Security $security
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Only on main request
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Check if admin just logged in
        if ($request->getPathInfo() === '/admin' && $request->getMethod() === 'GET') {
            $user = $this->security->getUser();

            if ($user instanceof User && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $session = $request->getSession();

                // If enrollment required, redirect to verify-access
                if ($user->requiresAdminStrongVerification() && !$session->get('admin_access_verified')) {
                    $session->set('admin_access_target', $this->urlGenerator->generate('admin_dashboard'));
                    $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin_verify_access')));
                }
            }
        }
    }
}
