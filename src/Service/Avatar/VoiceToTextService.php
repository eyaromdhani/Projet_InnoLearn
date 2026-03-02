<?php

namespace App\Service\Avatar;

use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VoiceToTextService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $openAiApiKey = '',
        private readonly string $whisperApiBaseUrl = 'https://api.openai.com/v1/audio/transcriptions'
    ) {
    }

    public function transcribe(string $audioFilePath): string
    {
        $apiUrl = $this->whisperApiBaseUrl;
        if ($apiUrl === '') {
            $apiUrl = 'https://api.openai.com/v1/audio/transcriptions';
        }

        $isDummyKey = str_contains(strtolower($this->openAiApiKey), 'xxxx') || str_contains(strtolower($this->openAiApiKey), 'example');

        $headers = [];
        if ($this->openAiApiKey !== '' && !$isDummyKey) {
            $headers['Authorization'] = 'Bearer ' . $this->openAiApiKey;
        } elseif (str_contains($apiUrl, 'api.openai.com')) {
            throw new \RuntimeException('A valid API key is missing. Cannot transcribe voice with OpenAI.');
        }

        // For local whisper implementations (LocalAI), an empty or dummy key is fine if they don't require auth.

        $formFields = [
            'file' => DataPart::fromPath($audioFilePath),
            'model' => 'whisper-1',
        ];
        $formData = new FormDataPart($formFields);

        $response = $this->httpClient->request('POST', $apiUrl, [
            'headers' => array_merge($headers, $formData->getPreparedHeaders()->toArray()),
            'body' => $formData->bodyToIterable(),
            'timeout' => 60,
        ]);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('OpenAI Whisper API error: ' . $response->getContent(false));
        }

        $data = $response->toArray();
        return (string) ($data['text'] ?? '');
    }
}
