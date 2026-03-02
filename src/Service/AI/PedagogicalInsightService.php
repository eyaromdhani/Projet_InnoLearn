<?php

namespace App\Service\AI;

use App\Entity\Cours;

class PedagogicalInsightService
{
    /**
     * Predicts course engagement based on pedagogical metadata.
     */
    public function predictPopularity(Cours $course): array
    {
        $text = strtolower((string) $course->getNom() . ' ' . $course->getDescription());
        $score = 50; // Neutral base

        $popularKeywords = ['ia', 'python', 'developpement', 'innovation', 'pratique', 'workshop'];
        foreach ($popularKeywords as $kw) {
            if (str_contains($text, $kw)) {
                $score += 10;
            }
        }

        if ($course->getDuree() > 20) {
            $score += 5; // Long form content adds value
        }

        return [
            'score' => min(100, $score),
            'prediction' => $score > 70 ? 'High Engagement' : 'Moderate Engagement',
            'suggestion' => $score < 60 ? 'Consider adding practical projects or modern tech keywords.' : 'Well-structured for high learner retention.'
        ];
    }
}
