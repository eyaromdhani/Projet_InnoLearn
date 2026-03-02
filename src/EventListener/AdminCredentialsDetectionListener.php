<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminCredentialsDetectionListener implements EventSubscriberInterface
{
    private const ADMIN_KEYWORDS = [
        'admin',
        'administrator',
        'root',
        'test',
        'superuser',
        'sudo',
        'system',
        'manager',
        'support',
    ];

    private const COMMON_WEAK_PASSWORDS = [
        'password',
        '12345678',
        '123456789',
        'password123',
        'admin123',
        'root123',
        'qwerty',
        'abc123',
        'letmein',
        'welcome',
        'monkey',
        '1234567890',
        '<PASSWORD>',
        'admin',
        'pass',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 11],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Only analyze POST requests to login endpoints
        if ($request->getMethod() !== 'POST') {
            return;
        }

        $pathInfo = $request->getPathInfo();
        if (strpos($pathInfo, 'login') === false && strpos($pathInfo, 'auth') === false) {
            return;
        }

        // Get login form data
        $email = $request->request->get('email') ?? '';
        $username = $request->request->get('username') ?? '';
        $password = $request->request->get('password') ?? '';

        $loginAttempt = $email ?: $username;
        $loginAttemptLower = strtolower($loginAttempt);

        // Check for admin keywords in login
        foreach (self::ADMIN_KEYWORDS as $keyword) {
            if (strpos($loginAttemptLower, strtolower($keyword)) !== false) {
                $message = sprintf(
                    "Attempted login with admin credential pattern '%s'",
                    $keyword
                );
                error_log(sprintf(
                    "⚠️ SECURITY ALERT: %s from IP %s\nLogin Attempt: %s\nRequest Path: %s",
                    $message,
                    $request->getClientIp(),
                    $loginAttempt,
                    $pathInfo
                ));
                $this->logFrontendAlert('admin_credential_pattern', 'warning', $message, [
                    'ip' => $request->getClientIp(),
                    'path' => $pathInfo,
                    'login' => $loginAttempt,
                ]);

                break; // Log only once per request
            }
        }

        // Check for common weak passwords
        $passwordLower = strtolower($password);
        foreach (self::COMMON_WEAK_PASSWORDS as $weakPassword) {
            if ($passwordLower === strtolower($weakPassword)) {
                $message = 'Login attempt with common weak password detected';
                error_log(sprintf(
                    "⚠️ SECURITY ALERT: %s from IP %s\nLogin: %s\nRequest Path: %s",
                    $message,
                    $request->getClientIp(),
                    $loginAttempt,
                    $pathInfo
                ));
                $this->logFrontendAlert('weak_password_attempt', 'warning', $message, [
                    'ip' => $request->getClientIp(),
                    'path' => $pathInfo,
                    'login' => $loginAttempt,
                ]);

                break; // Log only once per request
            }
        }
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
