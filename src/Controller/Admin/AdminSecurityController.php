<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\AdminAccessVerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin')]
class AdminSecurityController extends AbstractController
{
    #[Route('/login.php', name: 'admin_login_legacy_php', methods: ['GET'])]
    #[Route('/login.php/', name: 'admin_login_legacy_php_slash', methods: ['GET'])]
    public function legacyAdminLoginPhpRedirect(): Response
    {
        return $this->redirectToRoute('admin_login');
    }

    #[Route('/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/verify-access', name: 'admin_verify_access', methods: ['GET', 'POST'])]
    public function verifyAccess(Request $request, AdminAccessVerificationService $adminAccessVerificationService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User || !in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('admin_login');
        }

        $session = $request->getSession();
        if ($session->get('admin_access_verified') === true && $request->isMethod('GET')) {
            $session->remove('admin_access_target');
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('admin-verify-access', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Invalid security token.');
                return $this->redirectToRoute('admin_verify_access');
            }

            $hardwareKey = (string) $request->request->get('hardware_key_plain', '');
            $faceReference = $request->files->get('face_reference');
            $temporaryFaceUpload = null;

            if (!$faceReference instanceof UploadedFile) {
                $faceReferenceData = trim((string) $request->request->get('face_reference_data', ''));
                if ($faceReferenceData !== '') {
                    $temporaryFaceUpload = $this->createUploadedFaceFromBase64($faceReferenceData);
                    if ($temporaryFaceUpload instanceof UploadedFile) {
                        $faceReference = $temporaryFaceUpload;
                    }
                }
            }

            $isVerified = $faceReference instanceof UploadedFile
                && $adminAccessVerificationService->verify($user, $hardwareKey, $faceReference);

            if ($temporaryFaceUpload instanceof UploadedFile) {
                @unlink($temporaryFaceUpload->getPathname());
            }

            if ($isVerified) {
                $session->set('admin_access_verified', true);
                $target = $session->get('admin_access_target', $this->generateUrl('admin_dashboard'));
                $session->remove('admin_access_target');
                $this->addFlash('success', 'Hardware key and face verification passed.');
                return $this->redirect($target);
            }

            $this->addFlash('danger', 'Hardware key or face verification failed.');
            return $this->redirectToRoute('admin_verify_access');
        }

        return $this->render('admin/security/verify_access.html.twig');
    }

    #[Route('/logout', name: 'admin_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function createUploadedFaceFromBase64(string $base64Data): ?UploadedFile
    {
        if (str_starts_with($base64Data, 'data:image')) {
            $commaPos = strpos($base64Data, ',');
            if ($commaPos === false) {
                return null;
            }
            $base64Data = substr($base64Data, $commaPos + 1);
        }

        $imageData = base64_decode($base64Data, true);
        if ($imageData === false || $imageData === '') {
            return null;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'admin_face_');
        if ($tempPath === false) {
            return null;
        }

        if (file_put_contents($tempPath, $imageData) === false) {
            @unlink($tempPath);
            return null;
        }

        return new UploadedFile(
            $tempPath,
            'face_capture.jpg',
            'image/jpeg',
            null,
            true
        );
    }
}
