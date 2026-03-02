<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PersonalizationService
{
    private string $pythonServiceUrl;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        string $pythonServiceUrl = 'http://localhost:8001'
    ) {
        $this->pythonServiceUrl = rtrim($pythonServiceUrl, '/');
    }

    /**
     * Get learning style and personality analysis from Python microservice
     */
    public function analyzeProfile(array $quizResponses): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->pythonServiceUrl . '/profile-user', [
                'json' => ['quiz_answers' => $quizResponses]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            // Fallback strategy if Python service is down
            return [
                'learning_style' => 'visual', // Default fallback
                'personality_tags' => ['neutral'],
                'recommendations' => ['Start with basic video content']
            ];
        }
    }

    /**
     * Get reordered content recommendations based on user profile
     */
    public function getAdaptiveContent(User $user, array $contentList): array
    {
        $profile = $user->getUserProfile();
        if (!$profile) {
            return $contentList;
        }

        try {
            $response = $this->httpClient->request('POST', $this->pythonServiceUrl . '/adaptive-content', [
                'json' => [
                    'user_profile' => [
                        'learning_style' => $profile->getLearningStyle(),
                        'personality_types' => $profile->getPersonalityType(),
                    ],
                    'content_list' => $contentList
                ]
            ]);

            return $response->toArray()['reordered_content'];
        } catch (\Exception $e) {
            return $contentList;
        }
    }
}
