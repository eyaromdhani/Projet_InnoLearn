<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isIsActive() || $user->isBanned()) {
            throw new CustomUserMessageAuthenticationException('Your account has been disabled or banned due to multiple failed login attempts.');
        }

        // Specifically block ROLE_ADMIN on the frontend login BEFORE password check for better performance
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $request = $this->requestStack->getCurrentRequest();
            $this->logFrontendAlert('admin_role_frontend_block', 'warning', 'ROLE_ADMIN login blocked on frontend', [
                'ip' => $request?->getClientIp(),
                'path' => $request?->getPathInfo(),
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
            ]);
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }

    private function logFrontendAlert(string $type, string $severity, string $message, array $context = []): void
    {
        $logPath = dirname(__DIR__, 2) . '/var/security/frontend-alerts.log';
        @mkdir(dirname($logPath), 0777, true);

        $entry = [
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
        ];

        @file_put_contents(
            $logPath,
            json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
