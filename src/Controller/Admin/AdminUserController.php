<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AdminAccessVerificationService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    #[Route('/', name: 'admin_user_index_slash', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $status = (string) $request->query->get('status', 'all');
        $role = (string) $request->query->get('role', 'all');

        $qb = $this->buildUsersQuery($userRepository, $search, $status, $role);
        $users = $qb->orderBy('u.id', 'DESC')->getQuery()->getResult();

        $totalUsers = (int) $userRepository->count([]);
        $activeUsers = (int) $userRepository->count(['isActive' => true]);
        $bannedUsers = (int) $userRepository->count(['isActive' => false]);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'stats' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'banned' => $bannedUsers,
                'filtered' => count($users),
            ],
        ]);
    }

    #[Route('/export', name: 'admin_user_export', methods: ['GET'])]
    public function export(Request $request, UserRepository $userRepository): StreamedResponse
    {
        $search = trim((string) $request->query->get('q', ''));
        $status = (string) $request->query->get('status', 'all');
        $role = (string) $request->query->get('role', 'all');

        $users = $this->buildUsersQuery($userRepository, $search, $status, $role)
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();

        $response = new StreamedResponse(function () use ($users): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['ID', 'Name', 'Username', 'Email', 'Roles', 'Status', 'Phone']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->getId(),
                    $user->getName(),
                    $user->getUsername(),
                    $user->getEmail(),
                    implode('|', $user->getRoles()),
                    $user->isIsActive() ? 'Active' : 'Banned',
                    ($user->getCountryCode() ?? '') . ($user->getPhoneNumber() ?? ''),
                ]);
            }

            fclose($handle);
        });

        $filename = sprintf('users-export-%s.csv', (new \DateTime())->format('Y-m-d_H-i-s'));
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));

        return $response;
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, AdminAccessVerificationService $adminAccessVerificationService): Response
    {
        $user = new User();
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user-form', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }
            $user->setEmail($request->request->get('email'));
            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $role = $request->request->get('role', 'ROLE_USER');
            $user->setRoles([$role]);
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setCountryCode($request->request->get('countryCode'));

            $hardwareKeyPlain = trim((string) $request->request->get('hardware_key_plain', ''));
            $faceReferenceData = trim((string) $request->request->get('face_reference_data', ''));

            if ($role === 'ROLE_ADMIN') {
                if ($hardwareKeyPlain === '' || $faceReferenceData === '') {
                    $this->addFlash('danger', 'Admin creation requires hardware key and face capture.');

                    return $this->render('admin/user/new.html.twig', [
                        'user' => $user,
                    ]);
                }

                $user->setAdminHardwareKeyHash($adminAccessVerificationService->hashHardwareKey($hardwareKeyPlain));
                
                // Convert base64 to temp file for face processing
                $faceHash = $this->processFaceReferenceData($faceReferenceData, $adminAccessVerificationService);
                if ($faceHash === null) {
                    $this->addFlash('danger', 'Invalid face reference image for admin user.');

                    return $this->render('admin/user/new.html.twig', [
                        'user' => $user,
                    ]);
                }
                $user->setAdminFaceSignatureHash($faceHash);
            } else {
                $user->setAdminHardwareKeyHash(null);
                $user->setAdminFaceSignatureHash(null);
            }
            
            $password = $request->request->get('password');
            if ($password) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }

            $duplicateError = $this->findDuplicateUserError($userRepository, $user->getEmail(), $user->getUsername(), null);
            if ($duplicateError !== null) {
                $this->addFlash('danger', $duplicateError);

                return $this->render('admin/user/new.html.twig', [
                    'user' => $user,
                ]);
            }

            try {
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Email or username already exists. Please choose another one.');

                return $this->render('admin/user/new.html.twig', [
                    'user' => $user,
                ]);
            }

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, AdminAccessVerificationService $adminAccessVerificationService): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user-form', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }
            $user->setEmail($request->request->get('email'));
            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $role = $request->request->get('role', 'ROLE_USER');
            $user->setRoles([$role]);
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setCountryCode($request->request->get('countryCode'));

            $hardwareKeyPlain = trim((string) $request->request->get('hardware_key_plain', ''));
            $faceReferenceData = trim((string) $request->request->get('face_reference_data', ''));

            if ($role === 'ROLE_ADMIN') {
                if ($hardwareKeyPlain !== '') {
                    $user->setAdminHardwareKeyHash($adminAccessVerificationService->hashHardwareKey($hardwareKeyPlain));
                }

                if ($faceReferenceData !== '') {
                    $faceHash = $this->processFaceReferenceData($faceReferenceData, $adminAccessVerificationService);
                    if ($faceHash === null) {
                        $this->addFlash('danger', 'Invalid face reference image for admin user.');

                        return $this->render('admin/user/edit.html.twig', [
                            'user' => $user,
                        ]);
                    }
                    $user->setAdminFaceSignatureHash($faceHash);
                }

                if ($user->getAdminHardwareKeyHash() === null || $user->getAdminFaceSignatureHash() === null) {
                    $this->addFlash('danger', 'Admin user must have both hardware key and face reference enrolled.');

                    return $this->render('admin/user/edit.html.twig', [
                        'user' => $user,
                    ]);
                }
            } else {
                $user->setAdminHardwareKeyHash(null);
                $user->setAdminFaceSignatureHash(null);
            }

            $password = $request->request->get('password');
            if ($password) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }

            $duplicateError = $this->findDuplicateUserError($userRepository, $user->getEmail(), $user->getUsername(), $user->getId());
            if ($duplicateError !== null) {
                $this->addFlash('danger', $duplicateError);

                return $this->render('admin/user/edit.html.twig', [
                    'user' => $user,
                ]);
            }

            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException) {
                $this->addFlash('danger', 'Email or username already exists. Please choose another one.');

                return $this->render('admin/user/edit.html.twig', [
                    'user' => $user,
                ]);
            }

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_user_toggle', methods: ['POST'])]
    public function toggleStatus(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(!$user->isIsActive());
        $entityManager->flush();

        $this->addFlash('success', 'Statut de l\'utilisateur mis à jour.');
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/detect-usb-key', name: 'admin_user_detect_usb', methods: ['POST'])]
    public function detectUsbKey(#[\Symfony\Component\DependencyInjection\Attribute\Autowire('%kernel.project_dir%')] string $projectDir): Response
    {
        try {
            $scriptPath = $projectDir . '/scripts/detect_usb_key.ps1';

            if (!is_file($scriptPath)) {
                return $this->json([
                    'success' => false,
                    'error' => 'USB detection script not found.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Execute PowerShell script to detect USB key
            $output = [];
            $returnVar = 0;
            $command = 'powershell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File ' . escapeshellarg($scriptPath) . ' 2>&1';
            exec($command, $output, $returnVar);

            $output = array_filter(array_map(static fn ($line) => trim((string) $line), $output), static fn ($line) => $line !== '');
            $hardware_key = implode("\n", $output);
            $hardware_key = trim($hardware_key);

            if ($returnVar !== 0 || empty($hardware_key) || strlen($hardware_key) < 10) {
                return $this->json([
                    'success' => false,
                    'error' => 'No USB drive detected or script failed. Please insert a USB drive.'
                ], Response::HTTP_BAD_REQUEST);
            }

            return $this->json([
                'success' => true,
                'hardware_key' => $hardware_key
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }

    private function buildUsersQuery(UserRepository $userRepository, string $search, string $status, string $role)
    {
        $qb = $userRepository->createQueryBuilder('u');

        if ($search !== '') {
            $qb
                ->andWhere('u.name LIKE :q OR u.username LIKE :q OR u.email LIKE :q')
                ->setParameter('q', '%' . $search . '%');
        }

        if ($status === 'active') {
            $qb->andWhere('u.isActive = :isActive')->setParameter('isActive', true);
        } elseif ($status === 'banned') {
            $qb->andWhere('u.isActive = :isActive')->setParameter('isActive', false);
        }

        if ($role !== 'all') {
            $qb
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', '%"' . $role . '"%');
        }

        return $qb;
    }

    private function findDuplicateUserError(UserRepository $userRepository, ?string $email, ?string $username, ?int $excludeUserId): ?string
    {
        if ($email !== null && $email !== '') {
            $existingByEmail = $userRepository->findOneBy(['email' => $email]);
            if ($existingByEmail !== null && $existingByEmail->getId() !== $excludeUserId) {
                return 'This email is already used by another user.';
            }
        }

        if ($username !== null && $username !== '') {
            $existingByUsername = $userRepository->findOneBy(['username' => $username]);
            if ($existingByUsername !== null && $existingByUsername->getId() !== $excludeUserId) {
                return 'This username is already used by another user.';
            }
        }

        return null;
    }

    private function processFaceReferenceData(string $base64Data, AdminAccessVerificationService $service): ?string
    {
        // Remove data:image/...;base64, prefix if present
        if (str_starts_with($base64Data, 'data:image')) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }
        
        $imageData = base64_decode($base64Data, true);
        if ($imageData === false) {
            return null;
        }
        
        // Create temporary file
        $tempFile = tmpfile();
        if ($tempFile === false) {
            return null;
        }
        
        $tempPath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempPath, $imageData);
        
        // Create UploadedFile mock
        $uploadedFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $tempPath,
            'face_capture.jpg',
            'image/jpeg',
            null,
            true
        );
        
        $hash = $service->hashFaceReference($uploadedFile);
        
        fclose($tempFile);
        
        return $hash;
    }
}
