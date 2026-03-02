<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SmsService;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager,
        private SmsService $smsService,
        private KernelInterface $kernel
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        /** @var \App\Entity\User $user */
        $user = $token->getUser();
        $roles = $token->getRoleNames();

        error_log("LoginSuccessHandler triggered for user: " . $user->getUserIdentifier());
        error_log("Current MFA Status: " . ($request->getSession()->get('mfa_verified') ? 'VERIFIED' : 'NOT VERIFIED'));

        // --- MFA INTERCEPTOR ---
        // If the user hasn't completed MFA yet (session flag mfa_verified is not true)
        if (!$request->getSession()->get('mfa_verified')) {
            // 1. Generate 8-digit key (to match your Entity length of 8)
            $key = (string) random_int(10000000, 99999999);
            $keyAuthDevMode = $this->kernel->getEnvironment() === 'dev' || (($_ENV['MFA_KEY_DEV_MODE'] ?? '0') === '1');
            
            // 2. Save to User entity
            $user->setVerificationKey($key);
            $user->setKeyExpiresAt(new \DateTime('+10 minutes'));
            $this->entityManager->flush();

            if ($keyAuthDevMode) {
                $request->getSession()->set('mfa_dev_code', $key);
                /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
                $session = $request->getSession();
                $session->getFlashBag()->add('info', 'MFA dev mode active: verification code is displayed on screen.');
            } else {
                $request->getSession()->remove('mfa_dev_code');
            }

            // 3. Logic Split: Signup Verification (SMS) vs Login MFA (Email)
            if ($keyAuthDevMode) {
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] 🧪 DEV MODE MFA CODE for " . $user->getUserIdentifier() . ": {$key}\n", FILE_APPEND);
            } elseif (!$user->isPhoneVerified()) {
                // --- SIGNUP FLOW (Phone Verification) ---
                $phoneNumber = $user->getPhoneNumber();
                $countryCode = $user->getCountryCode();

                if ($phoneNumber && $countryCode) {
                    $fullPhoneNumber = $this->smsService->formatPhoneNumber($countryCode, $phoneNumber);
                    $this->smsService->sendVerificationCode($fullPhoneNumber, $key);
                    
                    // Log for debug
                    file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] 📱 SMS Sent for Signup Verification to {$fullPhoneNumber}\n", FILE_APPEND);
                } else {
                    $msg = "MFA Warning: User " . $user->getUserIdentifier() . " has no phone number. SMS not sent.";
                    error_log($msg);
                    file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] ⚠️ " . $msg . "\n", FILE_APPEND);
                }
            } else {
                // --- LOGIN FLOW (Email Verification) ---
                // Since MAILER_DSN is null, we simulate email sending by logging it.
                // In a real scenario, use $this->mailer->send(...)
                
                $email = $user->getEmail();
                $msg = "📧 EMAIL MFA CODE for {$email}: {$key} (Simulated - Check logs)";
                
                // Log strictly
                file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n", FILE_APPEND);
                
                // Add a flash message to inform user (needs session)
                /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
                $session = $request->getSession();
                $session->getFlashBag()->add('info', 'Verification code sent to your email (Dev Mode: Check logs)');
            }

            // 4. Mark the session as "In-Between Steps"
            $request->getSession()->set('mfa_user_id', $user->getId());
            $request->getSession()->set('mfa_status', 'pending_key');

            // 5. STOP the normal redirect and send them back to login for the Key
            $logMessage = sprintf("[%s] MFA Key for %s: %s\n", date('Y-m-d H:i:s'), $user->getUserIdentifier(), $key);
            file_put_contents(__DIR__ . '/../../var/log/mfa_debug.log', $logMessage, FILE_APPEND);
            
            error_log("Redirecting to app_login for MFA verification. Key generated: " . $key);
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }

        // --- Standard Success Redirection ---
        if (in_array('ROLE_INSTRUCTOR', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_enseignant_home'));
        }

        if (in_array('ROLE_RECRUITER', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_recruiter_home'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_student_home'));
    }
}