<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

class MfaController extends AbstractController
{
    #[Route('/mfa/verify', name: 'app_mfa_verify', methods: ['POST'])]
    public function verifyKey(Request $request, EntityManagerInterface $entityManager): Response
    {
        $submittedKey = preg_replace('/\D+/', '', (string) $request->request->get('verification_key', ''));
        $mfaUserId = $request->getSession()->get('mfa_user_id');
        $mfaStatus = $request->getSession()->get('mfa_status');

        // Security check: ensure we're in MFA pending state
        if ($mfaStatus !== 'pending_key' || !$mfaUserId) {
            $this->addFlash('error', 'Invalid MFA session. Please log in again.');
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find($mfaUserId);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_login');
        }

        // Check if key matches and hasn't expired
        if ($user->getVerificationKey() !== $submittedKey) {
            $this->addFlash('error', 'Invalid verification code.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->getKeyExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Verification code expired. Please log in again.');
            return $this->redirectToRoute('app_login');
        }

        // SUCCESS: Mark MFA as verified
        $request->getSession()->set('mfa_verified', true);
        $request->getSession()->remove('mfa_user_id');
        $request->getSession()->remove('mfa_status');
        $request->getSession()->remove('mfa_dev_code');
        
        // Mark user's phone as verified (critical for subsequent logins to use Email instead of SMS)
        if (!$user->isPhoneVerified()) {
            $user->setIsPhoneVerified(true);
            $entityManager->flush();
            $this->addFlash('success', 'Phone number verified successfully!');
        }

        // Redirect based on role
        $roles = $user->getRoles();
        if (in_array('ROLE_INSTRUCTOR', $roles, true)) {
            return $this->redirectToRoute('app_enseignant_home');
        }
        if (in_array('ROLE_RECRUITER', $roles, true)) {
            return $this->redirectToRoute('app_recruiter_home');
        }

        return $this->redirectToRoute('app_student_home');
    }
}
