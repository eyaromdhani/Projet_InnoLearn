<?php

namespace App\Service\Avatar;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LlmIntentService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $openAiApiKey = '',
        private readonly string $ollamaBaseUrl = '',
        private readonly string $ollamaModel = 'llama3.1:8b',
        private readonly string $ollamaVisionModel = 'llava:7b'
    ) {
    }

    public function analyze(string $description, ?string $selfieAbsolutePath = null): array
    {
        $description = trim($description);
        if ($description === '') {
            return $this->localIntent('default avatar', 'empty_description');
        }

        $imageTraits = $this->extractImageTraits($selfieAbsolutePath);

        if ($this->canCallOllama()) {
            $ollamaIntent = $this->analyzeWithOllama($description, $imageTraits);
            if (is_array($ollamaIntent)) {
                return $ollamaIntent;
            }
        }

        if ($this->canCallOpenAi()) {
            $openAiIntent = $this->analyzeWithOpenAi($description, $imageTraits);
            if (is_array($openAiIntent)) {
                return $openAiIntent;
            }
        }

        return $this->localIntent($description, 'free_local_fallback', $imageTraits);
    }

    private function analyzeWithOpenAi(string $description, array $imageTraits = []): ?array
    {
        $imageHints = $imageTraits !== [] ? json_encode($imageTraits, JSON_UNESCAPED_UNICODE) : '{}';

        $prompt = <<<PROMPT
You are an avatar intent parser.
Return ONLY valid JSON with this shape:
{
  "intent": "short user intent",
  "hair": "...",
  "outfit": "...",
  "body_type": "...",
  "style": "realistic|anime|cartoon",
    "provider": "readyplayerme",
  "provider_parameters": {
        "rpm_avatar_id": "optional avatar id if provided",
        "quality": "low|medium|high",
        "lod": 0,
        "textureAtlas": "none|256|512|1024",
        "textureFormat": "webp|jpeg|png",
        "textureQuality": "low|medium|high",
        "pose": "A|T",
        "useDracoMeshCompression": false,
        "useHands": true
  }
}
User description: {$description}
    Image extracted traits: {$imageHints}
PROMPT;

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You extract avatar generation intent and output strict JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ],
                'timeout' => 20,
            ]);

            $data = $response->toArray(false);
            $content = $data['choices'][0]['message']['content'] ?? '';
            $decoded = $this->decodeModelJson((string) $content);

            if (!is_array($decoded)) {
                return $this->localIntent($description, 'invalid_model_json');
            }

            return $this->normalizeIntent($decoded, $description, 'openai', $imageTraits);
        } catch (\Throwable) {
            return null;
        }
    }

    private function analyzeWithOllama(string $description, array $imageTraits = []): ?array
    {
        $imageHints = $imageTraits !== [] ? json_encode($imageTraits, JSON_UNESCAPED_UNICODE) : '{}';

        $prompt = <<<PROMPT
Return ONLY valid JSON with this shape:
{
  "intent": "short user intent",
  "hair": "...",
  "outfit": "...",
  "body_type": "...",
  "style": "realistic|anime|cartoon",
    "provider": "readyplayerme",
  "provider_parameters": {
        "rpm_avatar_id": "optional avatar id if provided",
        "quality": "low|medium|high",
        "lod": 0,
        "textureAtlas": "none|256|512|1024",
        "textureFormat": "webp|jpeg|png",
        "textureQuality": "low|medium|high",
        "pose": "A|T",
        "useDracoMeshCompression": false,
        "useHands": true
  }
}
User description: {$description}
    Image extracted traits: {$imageHints}
PROMPT;

        try {
            $response = $this->httpClient->request('POST', rtrim($this->ollamaBaseUrl, '/') . '/api/generate', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->ollamaModel,
                    'prompt' => $prompt,
                    'stream' => false,
                    'format' => 'json',
                ],
                'timeout' => 30,
            ]);

            $data = $response->toArray(false);
            $content = (string) ($data['response'] ?? '');
            $decoded = $this->decodeModelJson($content);

            if (!is_array($decoded)) {
                return null;
            }

            $intent = $this->normalizeIntent($decoded, $description, 'ollama', $imageTraits);
            $intent['provider_parameters']['free_mode'] = true;

            return $intent;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeIntent(array $decoded, string $description, string $source, array $imageTraits = []): array
    {
        $providerParams = is_array($decoded['provider_parameters'] ?? null)
            ? $decoded['provider_parameters']
            : [];

        $providerParams = $this->normalizeReadyPlayerMeParams($providerParams, $description);
        if ($imageTraits !== []) {
            $providerParams['image_traits'] = $imageTraits;
        }

        return [
            'intent' => $decoded['intent'] ?? $description,
            'hair' => $decoded['hair'] ?? 'unspecified',
            'outfit' => $decoded['outfit'] ?? 'casual',
            'body_type' => $decoded['body_type'] ?? 'medium',
            'style' => $decoded['style'] ?? 'realistic',
            'provider' => 'readyplayerme',
            'provider_parameters' => $providerParams,
            'validated' => true,
            'llm_source' => $source,
        ];
    }

    private function canCallOpenAi(): bool
    {
        if ($this->openAiApiKey === '' || !str_starts_with($this->openAiApiKey, 'sk-')) {
            return false;
        }

        $normalized = strtolower($this->openAiApiKey);
        return !str_contains($normalized, 'xxxx') && !str_contains($normalized, 'example') && !str_contains($normalized, 'replace');
    }

    private function canCallOllama(): bool
    {
        return trim($this->ollamaBaseUrl) !== '' && trim($this->ollamaModel) !== '';
    }

    private function canCallOllamaVision(): bool
    {
        return trim($this->ollamaBaseUrl) !== '' && trim($this->ollamaVisionModel) !== '';
    }

    private function localIntent(string $description, string $reason = 'local_nlp', array $imageTraits = []): array
    {
        $normalized = strtolower($description);

        $style = 'realistic';
        if (str_contains($normalized, 'anime')) {
            $style = 'anime';
        } elseif (str_contains($normalized, 'cartoon') || str_contains($normalized, 'toon')) {
            $style = 'cartoon';
        }

        $hair = 'unspecified';
        if (str_contains($normalized, 'short hair')) {
            $hair = 'short';
        } elseif (str_contains($normalized, 'long hair')) {
            $hair = 'long';
        } elseif (str_contains($normalized, 'curly')) {
            $hair = 'curly';
        }

        $outfit = 'casual';
        if (str_contains($normalized, 'hoodie')) {
            $outfit = 'hoodie';
        } elseif (str_contains($normalized, 'suit')) {
            $outfit = 'suit';
        } elseif (str_contains($normalized, 'sport')) {
            $outfit = 'sport';
        }

        $bodyType = 'medium';
        if (str_contains($normalized, 'athletic')) {
            $bodyType = 'athletic';
        } elseif (str_contains($normalized, 'slim')) {
            $bodyType = 'slim';
        } elseif (str_contains($normalized, 'muscular')) {
            $bodyType = 'muscular';
        }

        $providerParams = $this->normalizeReadyPlayerMeParams([
            'quality' => 'high',
            'lod' => 0,
            'textureAtlas' => 1024,
            'textureFormat' => 'webp',
            'textureQuality' => 'high',
            'pose' => 'A',
            'useDracoMeshCompression' => false,
            'derived_from_text' => true,
        ], $description);
        if ($imageTraits !== []) {
            $providerParams['image_traits'] = $imageTraits;
        }

        return [
            'intent' => $description,
            'hair' => $hair,
            'outfit' => $outfit,
            'body_type' => $bodyType,
            'style' => $style,
            'provider' => 'readyplayerme',
            'provider_parameters' => $providerParams,
            'validated' => true,
            'llm_source' => 'local_nlp',
            'local_reason' => $reason,
            'free_mode' => true,
        ];
    }

    private function extractImageTraits(?string $selfieAbsolutePath): array
    {
        if (!$this->canCallOllamaVision() || $selfieAbsolutePath === null || !is_file($selfieAbsolutePath)) {
            return [];
        }

        try {
            $binary = file_get_contents($selfieAbsolutePath);
            if ($binary === false || $binary === '') {
                return [];
            }

            $response = $this->httpClient->request('POST', rtrim($this->ollamaBaseUrl, '/') . '/api/generate', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->ollamaVisionModel,
                    'prompt' => 'Analyze this face image for avatar creation and return ONLY JSON with keys: hair, skin_tone, face_shape, eye_color, facial_hair, glasses, age_range, gender_presentation, outfit_hint.',
                    'images' => [base64_encode($binary)],
                    'stream' => false,
                    'format' => 'json',
                ],
                'timeout' => 45,
            ]);

            $data = $response->toArray(false);
            $decoded = $this->decodeModelJson((string) ($data['response'] ?? ''));

            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function decodeModelJson(string $content): ?array
    {
        $trimmed = trim($content);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
            $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $firstBrace = strpos($trimmed, '{');
        $lastBrace = strrpos($trimmed, '}');
        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $slice = substr($trimmed, $firstBrace, $lastBrace - $firstBrace + 1);
            $decoded = json_decode($slice, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function normalizeReadyPlayerMeParams(array $params, string $description): array
    {
        $quality = strtolower((string) ($params['quality'] ?? 'high'));
        if (!in_array($quality, ['low', 'medium', 'high'], true)) {
            $quality = 'high';
        }

        $lod = (int) ($params['lod'] ?? 0);
        if (!in_array($lod, [0, 1, 2], true)) {
            $lod = 0;
        }

        $textureAtlas = (string) ($params['textureAtlas'] ?? '1024');
        if (!in_array($textureAtlas, ['none', '256', '512', '1024'], true)) {
            $textureAtlas = '1024';
        }

        $textureFormat = strtolower((string) ($params['textureFormat'] ?? 'webp'));
        if (!in_array($textureFormat, ['webp', 'jpeg', 'png'], true)) {
            $textureFormat = 'webp';
        }

        $textureQuality = strtolower((string) ($params['textureQuality'] ?? 'high'));
        if (!in_array($textureQuality, ['low', 'medium', 'high'], true)) {
            $textureQuality = 'high';
        }

        $pose = strtoupper((string) ($params['pose'] ?? 'A'));
        if (!in_array($pose, ['A', 'T'], true)) {
            $pose = 'A';
        }

        $avatarId = (string) ($params['rpm_avatar_id'] ?? '');
        if ($avatarId === '') {
            $avatarId = $this->extractReadyPlayerMeAvatarId($description);
        }

        return [
            'rpm_avatar_id' => $avatarId,
            'quality' => $quality,
            'lod' => $lod,
            'textureAtlas' => $textureAtlas,
            'textureFormat' => $textureFormat,
            'textureQuality' => $textureQuality,
            'pose' => $pose,
            'useDracoMeshCompression' => (bool) ($params['useDracoMeshCompression'] ?? false),
            'useHands' => (bool) ($params['useHands'] ?? true),
        ];
    }

    private function extractReadyPlayerMeAvatarId(string $text): string
    {
        if (preg_match('/models\.readyplayer\.me\/([A-Za-z0-9_-]+)\.glb/i', $text, $matches)) {
            return (string) $matches[1];
        }

        if (preg_match('/\b([a-f0-9]{24})\b/i', $text, $matches)) {
            return (string) $matches[1];
        }

        return '';
    }
}
