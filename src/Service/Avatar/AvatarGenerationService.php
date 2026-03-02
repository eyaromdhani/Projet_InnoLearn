<?php

namespace App\Service\Avatar;

use App\Entity\AvatarGeneration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AvatarGenerationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LlmIntentService $llmIntentService,
        private readonly AvatarApiClient $avatarApiClient,
        private readonly HttpClientInterface $httpClient,
        private readonly string $projectDir
    ) {
    }

    public function generate(User $user, ?UploadedFile $selfie, string $description, ?string $prebuiltGlbUrl = null): AvatarGeneration
    {
        $description = trim($description);
        if ($description === '' && $selfie) {
            $description = 'Generate avatar from uploaded selfie with realistic style.';
        }

        $storedSelfieAbsolutePath = null;
        $storedSelfieWebPath = null;

        $avatarGeneration = new AvatarGeneration();
        $avatarGeneration->setUser($user);
        $avatarGeneration->setDescription($description);
        $avatarGeneration->setStatus('processing');

        if ($selfie) {
            $selfieStorage = $this->storeSelfie($selfie);
            $storedSelfieAbsolutePath = $selfieStorage['absolute'];
            $storedSelfieWebPath = $selfieStorage['web'];
            $avatarGeneration->setSelfiePath($storedSelfieWebPath);
        }

        if ($prebuiltGlbUrl !== null && filter_var($prebuiltGlbUrl, FILTER_VALIDATE_URL)) {
            $avatarGeneration->setProvider('avaturn');
            $avatarGeneration->setProviderParameters([
                'source' => 'avaturn_sdk_export',
                'avaturn_glb_url' => $prebuiltGlbUrl,
            ]);
            $avatarGeneration->setIntentData([
                'intent' => $description !== '' ? $description : 'Avatar created from Avaturn export',
                'provider' => 'avaturn',
                'llm_source' => 'avaturn_sdk',
                'validated' => true,
            ]);
            $avatarGeneration->setExternalJobId('avaturn-export-' . uniqid());
            $avatarGeneration->setStatus('completed');

            $localGlb = $this->downloadAndStoreGlb($prebuiltGlbUrl);
            $avatarGeneration->setGlbUrl($localGlb ?? $prebuiltGlbUrl);
            $avatarGeneration->setStoragePath($localGlb ?? $prebuiltGlbUrl);
            $avatarGeneration->touch();

            $this->entityManager->persist($avatarGeneration);
            $this->entityManager->flush();

            return $avatarGeneration;
        }

        $intent = $this->llmIntentService->analyze($description, $storedSelfieAbsolutePath);
        $avatarGeneration->setIntentData($intent);
        $avatarGeneration->setProvider($intent['provider'] ?? 'avatar_api');
        $providerParameters = $intent['provider_parameters'] ?? [];
        if ($storedSelfieWebPath) {
            $providerParameters['selfie_path'] = $storedSelfieWebPath;
        }
        $avatarGeneration->setProviderParameters($providerParameters);

        $this->entityManager->persist($avatarGeneration);
        $this->entityManager->flush();

        try {
            $result = $selfie
                ? $this->avatarApiClient->createAvatar($user, $intent)
                : $this->avatarApiClient->createAvatarFromDescription($user, $description);

            $avatarGeneration->setExternalJobId($result['job_id'] ?? null);
            $avatarGeneration->setStatus($result['status'] ?? 'completed');
            $remoteGlb = $result['glb_url'] ?? null;

            if ($remoteGlb) {
                $localGlb = $this->downloadAndStoreGlb($remoteGlb);
                $avatarGeneration->setGlbUrl($localGlb ?? $remoteGlb);
                $avatarGeneration->setStoragePath($localGlb ?? $remoteGlb);
            }

            if (!empty($result['provider_parameters']) && is_array($result['provider_parameters'])) {
                $avatarGeneration->setProviderParameters($result['provider_parameters']);
            }
        } catch (\Throwable $exception) {
            $avatarGeneration->setStatus('failed');
            $intent['error'] = $exception->getMessage();
            $avatarGeneration->setIntentData($intent);
        }

        $avatarGeneration->touch();
        $this->entityManager->flush();

        return $avatarGeneration;
    }

    private function storeSelfie(UploadedFile $selfie): array
    {
        $targetDir = $this->projectDir . '/public/uploads/avatar/selfies';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $extension = $selfie->guessExtension() ?: 'jpg';
        $filename = 'selfie_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $selfie->move($targetDir, $filename);

        return [
            'web' => '/uploads/avatar/selfies/' . $filename,
            'absolute' => $targetDir . '/' . $filename,
        ];
    }

    private function downloadAndStoreGlb(string $remoteUrl): ?string
    {
        try {
            $targetDir = $this->projectDir . '/public/uploads/avatar/generated';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            $filename = 'avatar_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.glb';
            $targetPath = $targetDir . '/' . $filename;

            $content = $this->httpClient->request('GET', $remoteUrl, ['timeout' => 30])->getContent(false);
            if ($content === '') {
                return null;
            }

            file_put_contents($targetPath, $content);

            return '/uploads/avatar/generated/' . $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
