<?php

namespace App\Service;

use App\Entity\Cours;
use App\Entity\UserProfile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StoryGeneratorService
{
    private string $pythonServiceUrl = 'http://localhost:8001';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    /**
     * Generate a story for a specific course module based on user profile
     */
    public function generateStory(Cours $course, ?UserProfile $profile): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->pythonServiceUrl . '/generate-story', [
                'json' => [
                    'course_title' => $course->getNom(),
                    'course_description' => $course->getDescription(),
                    'user_profile' => $profile ? [
                        'learning_style' => $profile->getLearningStyle(),
                        'personality_types' => $profile->getPersonalityType(),
                    ] : null,
                ]
            ]);

            return $response->toArray();
        } catch (\Exception $e) {
            // Fallback story
            return [
                'story_text' => "Welcome to the Zone of {$course->getNom()}. A challenge awaits you here. Explore the concepts and defeat the final boss (Quiz) to proceed.",
                'narrator_voice' => 'neutral',
                'environment_type' => 'dungeon'
            ];
        }
    }
}
