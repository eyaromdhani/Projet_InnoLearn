<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $geminiApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $geminiApiKey;
    }

    public function analyzeComplexity(string $title, string $description): string
    {
        if ($this->apiKey === 'VOTRE_CLE_API_GEMINI_ICI' || empty($this->apiKey)) {
            return 'Intermédiaire'; // Fallback
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

        $prompt = <<<EOT
Analyse le projet suivant et détermine son niveau de difficulté technique parmi : Débutant, Intermédiaire, Avancé.
Réponds UNIQUEMENT par un seul mot (le niveau choisi).

Titre : $title
Description : $description
EOT;

        try {
            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            ]);

            $data = $response->toArray();
            $result = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Intermédiaire';
            
            $result = trim($result);
            $allowed = ['Débutant', 'Intermédiaire', 'Avancé'];
            
            foreach ($allowed as $level) {
                if (stripos($result, $level) !== false) {
                    return $level;
                }
            }

            return 'Intermédiaire';
        } catch (\Exception $e) {
            return 'Intermédiaire';
        }
    }
}
