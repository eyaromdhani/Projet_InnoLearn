<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityToolDetectionListener implements EventSubscriberInterface
{
    private const MALICIOUS_TOOLS = [
        'sqlmap',
        'hydra',
        'john',
        'johntheripper',
        'curl',
        'burpsuite',
        'nmap',
        'nikto',
        'havij',
        'acunetix',
        'nessus',
        'zap',
        'masscan',
        'metasploit',
        'w3af',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 10],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $userAgent = (string) $request->headers->get('User-Agent', '');
        $userAgentLower = strtolower($userAgent);

        // Check for malicious tools in User-Agent
        foreach (self::MALICIOUS_TOOLS as $tool) {
            if (strpos($userAgentLower, strtolower($tool)) !== false) {
                $message = sprintf("Detected malicious tool '%s' in User-Agent", $tool);
                // Log critical security alert
                error_log(sprintf(
                    "🚨 URGENT SECURITY ALERT: %s from IP %s\nUser-Agent: %s\nRequest Path: %s\nRequest Method: %s",
                    $message,
                    $request->getClientIp(),
                    $userAgent,
                    $request->getPathInfo(),
                    $request->getMethod()
                ));
                $this->logFrontendAlert('security_tool_detected', 'urgent', $message, [
                    'ip' => $request->getClientIp(),
                    'path' => $request->getPathInfo(),
                    'method' => $request->getMethod(),
                    'user_agent' => $userAgent,
                ]);

                // Optional: Block the request
                // throw new HttpException(403, 'Access Denied');
            }
        }

        // Check for common SQL injection patterns
        $suspiciousPatterns = [
            "union",
            "select",
            "or 1=1",
            "drop",
            "table",
            "exec",
            "execute",
            "<script",
            "union select",
            "or true",
        ];

        $pathInfo = $request->getPathInfo();
        $queryString = (string) $request->getQueryString();
        $checkString = $pathInfo . '?' . $queryString;
        $checkStringLower = strtolower($checkString);

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($checkStringLower, strtolower($pattern)) !== false) {
                $message = sprintf("Detected suspicious request pattern '%s'", $pattern);
                error_log(sprintf(
                    "⚠️ SECURITY ALERT: %s from IP %s\nRequest: %s",
                    $message,
                    $request->getClientIp(),
                    $checkString
                ));
                $this->logFrontendAlert('suspicious_request_pattern', 'warning', $message, [
                    'ip' => $request->getClientIp(),
                    'path' => $request->getPathInfo(),
                    'query' => $queryString,
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
