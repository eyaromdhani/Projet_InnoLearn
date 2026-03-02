<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdminAccessVerificationService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $appSecret = 'innolearn-default-salt',
        private readonly string $faceMlServiceUrl = 'http://127.0.0.1:8002'
    ) {
    }

    public function hashHardwareKey(string $plainHardwareKey): string
    {
        $normalized = strtolower(trim($plainHardwareKey));
        return hash('sha256', $normalized . '|' . $this->appSecret);
    }

    public function hashFaceReference(UploadedFile $file): ?string
    {
        return $this->extractFaceEmbeddingTemplate($file);
    }

    public function verify(User $user, string $hardwareKey, ?UploadedFile $faceReference): bool
    {
        $expectedHardwareHash = $user->getAdminHardwareKeyHash();
        $expectedFaceTemplate = $user->getAdminFaceSignatureHash();

        if ($expectedHardwareHash === null || $expectedFaceTemplate === null) {
            return false;
        }

        $computedHardwareHash = $this->hashHardwareKey($hardwareKey);
        if (!hash_equals($expectedHardwareHash, $computedHardwareHash)) {
            return false;
        }

        if ($faceReference === null) {
            return false;
        }

        if ($this->isEmbeddingTemplate($expectedFaceTemplate)) {
            return $this->compareFaceWithStoredTemplate($expectedFaceTemplate, $faceReference);
        }

        // Legacy fallback for previously stored hash-based templates.
        $computedLegacyFaceHash = $this->computeLegacyFaceHash($faceReference);
        if ($computedLegacyFaceHash === null) {
            return false;
        }

        return hash_equals($expectedFaceTemplate, $computedLegacyFaceHash);
    }

    private function extractFaceEmbeddingTemplate(UploadedFile $file): ?string
    {
        try {
            $response = $this->httpClient->request('POST', rtrim($this->faceMlServiceUrl, '/') . '/face/embedding', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'file' => fopen($file->getPathname(), 'rb'),
                ],
                'timeout' => 15,
            ]);

            $data = $response->toArray(false);

            $embedding = $data['embedding'] ?? null;
            if (!is_array($embedding) || count($embedding) === 0) {
                return null;
            }

            // Return the raw embedding array as JSON
            return json_encode($embedding, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }
    }

    private function compareFaceWithStoredTemplate(string $faceTemplateJson, UploadedFile $probeFile): bool
    {
        try {
            $response = $this->httpClient->request('POST', rtrim($this->faceMlServiceUrl, '/') . '/face/compare', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'file' => fopen($probeFile->getPathname(), 'rb'),
                    'stored_embedding_json' => $faceTemplateJson,
                ],
                'timeout' => 20,
            ]);

            $data = $response->toArray(false);
            return isset($data['match']) && $data['match'] === true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function isEmbeddingTemplate(string $storedValue): bool
    {
        try {
            $decoded = json_decode($storedValue, true, 512, JSON_THROW_ON_ERROR);
            // Now it's just an array of floats directly
            return is_array($decoded) && count($decoded) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function computeLegacyFaceHash(UploadedFile $file): ?string
    {
        $mlSignature = $this->extractMlFaceSignature($file);
        if ($mlSignature === null) {
            return null;
        }

        return hash('sha256', $mlSignature . '|' . $this->appSecret);
    }

    private function extractMlFaceSignature(UploadedFile $file): ?string
    {
        try {
            $response = $this->httpClient->request('POST', rtrim($this->faceMlServiceUrl, '/') . '/face/signature', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'file' => fopen($file->getPathname(), 'rb'),
                ],
                'timeout' => 15,
            ]);

            $data = $response->toArray(false);
            $signature = $data['signature'] ?? null;
            if (!is_string($signature) || trim($signature) === '') {
                return null;
            }

            return trim($signature);
        } catch (\Throwable) {
            return null;
        }
    }
}
