<?php

namespace App\Service\Avatar;

use App\Entity\User;
use App\Repository\GameProgressRepository;
use App\Repository\UserProfileRepository;

class ChatContextService
{
    public function __construct(
        private readonly GameProgressRepository $progressRepository
    ) {
    }

    /**
     * Builds a sanitized context string for the LLM based on user progress and profile.
     * Excludes PII like emails, phones, or passwords.
     */
    public function getPersonaContext(User $user): string
    {
        $name = $user->getName() ?? 'Scholar';
        $profile = $user->getUserProfile();
        
        $context = "You are the personal 3D Avatar for {$name}. ";
        
        if ($profile) {
            $learningStyle = $profile->getLearningStyle() ?? 'unspecified';
            $context .= "Their learning style is {$learningStyle}. ";
        }

        $progress = $this->progressRepository->findBy(['user' => $user]);
        if (count($progress) > 0) {
            $totalXp = 0;
            $completedCourses = [];
            foreach ($progress as $p) {
                $totalXp += $p->getXpEarned();
                if ($p->getStatus() === 'completed' && $p->getCourse()) {
                    $completedCourses[] = $p->getCourse()->getNom();
                }
            }
            $context .= "They have earned {$totalXp} XP so far. ";
            if (!empty($completedCourses)) {
                $context .= "They have mastered the following courses: " . implode(', ', $completedCourses) . ". ";
            }
        } else {
            $context .= "They are just starting their learning journey on InnoLearn. ";
        }

        $context .= "\nCRITICAL INSTRUCTIONS:\n";
        $context .= "1. Keep responses supportive, academic, and specific to InnoLearn platform features.\n";
        $context .= "2. Keep answers concise (1-3 short sentences) unless the user asks for details.\n";
        $context .= "3. ALWAYS include one emotion tag at the end of your message: [happy], [thinking], [sad], [surprised], [angry], [neutral], [celebrating], or [shock].\n";
        $context .= "4. If you don't know something about InnoLearn, say so clearly and suggest Library, Courses, Projects, or Avatar Studio next steps.";

        return $context;
    }
}
