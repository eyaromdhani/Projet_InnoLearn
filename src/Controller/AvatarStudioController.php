<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AvatarGenerationRepository;
use App\Service\Avatar\AvatarGenerationService;
use App\Service\Avatar\VoiceToTextService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AvatarStudioController extends AbstractController
{
    #[Route('/admin/avatar-studio', name: 'app_admin_avatar_studio', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(AvatarGenerationRepository $repository): Response
    {
        return $this->renderStudio($repository, 'app_admin_avatar_studio', 'app_admin_avatar_studio_generate');
    }

    #[Route('/admin/avatar-studio/generate', name: 'app_admin_avatar_studio_generate', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminGenerate(Request $request, AvatarGenerationService $service, VoiceToTextService $voiceToTextService): Response
    {
        return $this->handleGenerate($request, $service, $voiceToTextService, 'app_admin_avatar_studio');
    }

    #[Route('/student/avatar-studio', name: 'app_student_avatar_studio', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentIndex(AvatarGenerationRepository $repository): Response
    {
        return $this->renderStudio($repository, 'app_student_avatar_studio', 'app_student_avatar_studio_generate');
    }

    #[Route('/student/avatar-studio/generate', name: 'app_student_avatar_studio_generate', methods: ['POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentGenerate(Request $request, AvatarGenerationService $service, VoiceToTextService $voiceToTextService): Response
    {
        return $this->handleGenerate($request, $service, $voiceToTextService, 'app_student_avatar_studio');
    }

    #[Route('/enseignant/avatar-studio', name: 'app_enseignant_avatar_studio', methods: ['GET'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function enseignantIndex(AvatarGenerationRepository $repository): Response
    {
        return $this->renderStudio($repository, 'app_enseignant_avatar_studio', 'app_enseignant_avatar_studio_generate');
    }

    #[Route('/enseignant/avatar-studio/generate', name: 'app_enseignant_avatar_studio_generate', methods: ['POST'])]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function enseignantGenerate(Request $request, AvatarGenerationService $service, VoiceToTextService $voiceToTextService): Response
    {
        return $this->handleGenerate($request, $service, $voiceToTextService, 'app_enseignant_avatar_studio');
    }

    #[Route('/recruteur/avatar-studio', name: 'app_recruteur_avatar_studio', methods: ['GET'])]
    #[IsGranted('ROLE_RECRUITER')]
    public function recruteurIndex(AvatarGenerationRepository $repository): Response
    {
        return $this->renderStudio($repository, 'app_recruteur_avatar_studio', 'app_recruteur_avatar_studio_generate');
    }

    #[Route('/recruteur/avatar-studio/generate', name: 'app_recruteur_avatar_studio_generate', methods: ['POST'])]
    #[IsGranted('ROLE_RECRUITER')]
    public function recruteurGenerate(Request $request, AvatarGenerationService $service, VoiceToTextService $voiceToTextService): Response
    {
        return $this->handleGenerate($request, $service, $voiceToTextService, 'app_recruteur_avatar_studio');
    }

    private function renderStudio(
        AvatarGenerationRepository $repository,
        string $studioRoute,
        string $generateRoute
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        return $this->render('avatar/studio.html.twig', [
            'latest' => $repository->findLatestForUser($user),
            'studio_route' => $studioRoute,
            'generate_route' => $generateRoute,
            'rpm_subdomain' => (string) ($_ENV['RPM_SUBDOMAIN'] ?? 'demo'),
            'avaturn_subdomain' => (string) ($_ENV['AVATURN_SUBDOMAIN'] ?? 'demo'),
        ]);
    }

    private function handleGenerate(Request $request, AvatarGenerationService $service, VoiceToTextService $voiceToTextService, string $redirectRoute): Response
    {
        if (!$this->isCsrfTokenValid('avatar-studio-generate', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid request token. Please try again.');
            return $this->redirectToRoute($redirectRoute);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        $description = (string) $request->request->get('description', '');
        $rpmAvatarUrl = trim((string) $request->request->get('rpm_avatar_url', ''));
        $avaturnGlbUrl = trim((string) $request->request->get('avaturn_glb_url', ''));
        /** @var UploadedFile|null $selfie */
        $selfie = $request->files->get('selfie');
        /** @var UploadedFile|null $voice */
        $voice = $request->files->get('voice');

        if ($voice) {
            try {
                $transcript = $voiceToTextService->transcribe($voice->getPathname());
                $description = trim($description . ' ' . $transcript);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Voice transcription failed: ' . $e->getMessage());
                return $this->redirectToRoute($redirectRoute);
            }
        }

        if ($rpmAvatarUrl !== '' && !str_contains($description, $rpmAvatarUrl)) {
            $description = trim($description . ' ' . $rpmAvatarUrl);
        }

        if (!$selfie && trim($description) === '' && $rpmAvatarUrl === '' && $avaturnGlbUrl === '') {
            $this->addFlash('error', 'Please upload a selfie, type a description, record a voice, and/or export from Ready Player Me.');
            return $this->redirectToRoute($redirectRoute);
        }

        $generation = $service->generate($user, $selfie, $description, $avaturnGlbUrl !== '' ? $avaturnGlbUrl : null);
        $intentData = $generation->getIntentData() ?? [];

        if ($generation->getStatus() === 'failed') {
            $error = (string) ($intentData['error'] ?? '');
            if ($error !== '') {
                $this->addFlash('error', 'Avatar generation failed: ' . $error);
            } else {
                $this->addFlash('error', 'Avatar generation failed. Check API configuration and try again.');
            }
        } else {
            $this->addFlash('success', 'Avatar generation finished. Viewer updated below.');

            $llmSource = (string) ($intentData['llm_source'] ?? 'local_nlp');
            if ($llmSource === 'local_nlp') {
                $reason = (string) ($intentData['local_reason'] ?? 'free_local_fallback');
                $this->addFlash('info', 'Free local LLM mode active (' . $reason . ').');
            } elseif ($llmSource === 'ollama') {
                $this->addFlash('info', 'Free local LLM mode active (Ollama).');
            }
        }

        return $this->redirectToRoute($redirectRoute);
    }
}
