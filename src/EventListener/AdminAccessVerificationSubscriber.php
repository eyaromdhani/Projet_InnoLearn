<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminAccessVerificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 8],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = (string) $request->getPathInfo();

        if (!str_starts_with($path, '/admin')) {
            return;
        }

        if (str_starts_with($path, '/admin/login') || str_starts_with($path, '/admin/logout') || str_starts_with($path, '/admin/verify-access')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();
        if (!$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return;
        }

        // Skip if admin account has not been enrolled yet.
        if (!$user->requiresAdminStrongVerification()) {
            return;
        }

        $session = $request->getSession();
        if ($session->get('admin_access_verified', false) === true) {
            return;
        }

        $session->set('admin_access_target', $request->getUri());

        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('admin_verify_access')));
    }
}
