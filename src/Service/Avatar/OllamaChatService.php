<?php

namespace App\Service\Avatar;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaChatService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ChatContextService $contextService,
        private readonly VoiceToTextService $voiceToTextService,
        private readonly string $ollamaBaseUrl = 'http://127.0.0.1:11434',
        private readonly string $ollamaModel = 'llama3.1:8b',
        private readonly string $ollamaVisionModel = 'llava:7b'
    ) {
    }

    /**
     * Handles a multimodal chat request.
     */
    public function chat(User $user, ?string $message = null, ?UploadedFile $voice = null, ?UploadedFile $image = null): array
    {
        $prompt = $message ?? '';

        // 1. Process Voice if present
        if ($voice) {
            try {
                $transcript = $this->voiceToTextService->transcribe($voice->getPathname());
                $prompt = trim($prompt . ' ' . $transcript);
            } catch (\Exception $e) {
                return ['message' => 'Sorry, I couldn\'t hear you clearly. Error: ' . $e->getMessage(), 'emotion' => 'sad'];
            }
        }

        // 2. Build Persona Context
        $systemPrompt = $this->contextService->getPersonaContext($user);

        // 3. Process Image if present (Vision)
        $images = [];
        if ($image) {
            $binary = file_get_contents($image->getPathname());
            $images[] = base64_encode($binary);
            $systemPrompt .= "\nThe user has also uploaded an image. Describe what you see and incorporate it into your helpful response.";
        }

        if ($prompt === '' && !$image) {
            return ['message' => 'I\'m listening! You can type, speak, or show me an image.', 'emotion' => 'neutral'];
        }

        try {
            $response = $this->httpClient->request('POST', rtrim($this->ollamaBaseUrl, '/') . '/api/generate', [
                'json' => [
                    'model' => $image ? $this->ollamaVisionModel : $this->ollamaModel,
                    'prompt' => "System: {$systemPrompt}\nUser: {$prompt}",
                    'stream' => false,
                    'images' => $images,
                    'options' => ['temperature' => 0.7]
                ],
                'timeout' => 60
            ]);

            $data = $response->toArray();
            $fullResponse = (string) ($data['response'] ?? '');

            // Extract emotion tag [emotion]
            $emotion = 'neutral';
            if (preg_match('/\[(happy|sad|surprised|angry|thinking|neutral|celebrating|shock)\]/i', $fullResponse, $matches)) {
                $emotion = strtolower($matches[1]);
                $fullResponse = trim(str_replace($matches[0], '', $fullResponse));
            }

            return [
                'message' => $fullResponse !== '' ? $fullResponse : 'I am here to help you in InnoLearn. What would you like to do next? [neutral]',
                'emotion' => $emotion,
                'prompt_used' => $prompt
            ];
        } catch (\Throwable $e) {
            return [
                'message' => 'I disconnected for a second! Make sure Ollama is running locally. Error: ' . $e->getMessage(),
                'emotion' => 'thinking'
            ];
        }
    }
}
