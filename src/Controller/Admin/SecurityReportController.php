<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/security')]
class SecurityReportController extends AbstractController
{
    #[Route('', name: 'admin_security_report', methods: ['GET'])]
    public function index(): Response
    {
        $reportFile = $this->getParameter('kernel.project_dir') . '/var/security/report.json';
        $composerAuditFile = $this->getParameter('kernel.project_dir') . '/var/security/composer-audit.json';
        $frontendAlertsFile = $this->getParameter('kernel.project_dir') . '/var/security/frontend-alerts.log';

        $report = null;
        $composerAudit = null;
        $lastScanned = null;
        $frontendAlerts = [];
        $frontendAlertSummary = [
            'admin_role_frontend_block' => 0,
            'admin_credential_pattern' => 0,
            'weak_password_attempt' => 0,
            'security_tool_detected' => 0,
            'suspicious_request_pattern' => 0,
        ];

        if (file_exists($reportFile)) {
            $reportData = json_decode(file_get_contents($reportFile), true);
            // Get the full report data (not nested under 'report' key)
            $report = $reportData;
            $lastScanned = $reportData['timestamp'] ?? null;
        }

        if (file_exists($composerAuditFile)) {
            $composerAudit = json_decode(file_get_contents($composerAuditFile), true);
        }

        if (file_exists($frontendAlertsFile)) {
            $lines = file($frontendAlertsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach (array_slice(array_reverse($lines), 0, 25) as $line) {
                $decoded = json_decode($line, true);
                if (!is_array($decoded)) {
                    continue;
                }

                $frontendAlerts[] = $decoded;
                $type = $decoded['type'] ?? null;
                if ($type && array_key_exists($type, $frontendAlertSummary)) {
                    $frontendAlertSummary[$type]++;
                }
            }
        }

        return $this->render('admin/security_report/index.html.twig', [
            'report' => $report,
            'composer_audit' => $composerAudit,
            'last_scanned' => $lastScanned,
            'frontend_alerts' => $frontendAlerts,
            'frontend_alert_summary' => $frontendAlertSummary,
        ]);
    }

    #[Route('/refresh', name: 'admin_security_report_refresh', methods: ['POST'])]
    public function refresh(): Response
    {
        try {
            $this->addFlash('success', 'Security scan initiated. Please refresh in a moment.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to initiate security scan: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_security_report');
    }
}
