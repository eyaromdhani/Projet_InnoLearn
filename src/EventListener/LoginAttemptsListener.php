<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginAttemptsListener implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private const MAX_ATTEMPTS = 5;
    private const RESET_AFTER_SECONDS = 3600; // 1 hour

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
            InteractiveLoginEvent::class => 'onInteractiveLogin',
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $request = $event->getRequest();
        $username = null;

        // Try to get from Passport
        if ($event->getPassport()) {
            try {
                $user = $event->getPassport()->getUser();
                if ($user instanceof User) {
                    $username = $user->getUserIdentifier();
                }
            } catch (\Exception $e) {
                // Passport user not available
            }
        }

        // Try to get from request form data
        if (!$username) {
            $username = $request->request->get('email') ?? $request->request->get('username');
        }

        // If still no username, try exception
        if (!$username && $event->getException()->getToken()) {
            $username = $event->getException()->getToken()->getUserIdentifier();
        }

        if (!$username) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $username]) 
            ?? $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            return;
        }

        // Get or initialize failed attempts count
        $failedAttempts = $user->getFailedLoginAttempts() ?? 0;
        $lastAttemptTime = $user->getLastFailedLoginAttempt();

        // Reset counter if more than 1 hour has passed
        if ($lastAttemptTime && (time() - $lastAttemptTime->getTimestamp()) > self::RESET_AFTER_SECONDS) {
            $failedAttempts = 0;
        }

        $failedAttempts++;

        // Update user entity
        $user->setFailedLoginAttempts($failedAttempts);
        $user->setLastFailedLoginAttempt(new \DateTime());

        // Auto-ban if max attempts exceeded
        if ($failedAttempts >= self::MAX_ATTEMPTS) {
            $user->setIsBanned(true);
            // Log the ban event
            error_log("🚨 SECURITY ALERT: User '{$username}' has been automatically banned after {$failedAttempts} failed login attempts");
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            // Reset failed attempts on successful login
            $user->setFailedLoginAttempts(0);
            $user->setLastFailedLoginAttempt(null);
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
