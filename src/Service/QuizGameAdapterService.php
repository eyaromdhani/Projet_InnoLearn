<?php

namespace App\Service;

use App\Entity\Quiz;

class QuizGameAdapterService
{
    /**
     * Map a standard quiz to a game-compatible "Boss Fight" structure
     */
    public function adaptForGame(\App\Entity\Formulaire $quiz): array
    {
        $questions = [];
        foreach ($quiz->getQuestions() as $question) {
            $questions[] = [
                'id' => $question->getId(),
                'prompt' => $question->getQuestionText(),
                'type' => $question->getType(),
                'options' => $this->parseOptions((string) $question->getCorrectAnswer()), // Logic would be more complex in real scenario
                'points' => $question->getPoints(),
                'mechanic' => $this->mapMechanic((string) $question->getType())
            ];
        }

        return [
            'boss_name' => "The Guardian of " . $quiz->getTitre(),
            'total_hp' => count($questions) * 10,
            'challenges' => $questions,
            'rewards' => [
                'xp' => 100,
                'stat_boost' => 'intelligence'
            ]
        ];
    }

    private function parseOptions(string $correctAnswer): array
    {
        // Placeholder for splitting answer strings into option arrays
        return [$correctAnswer, 'Alternative A', 'Alternative B', 'Alternative C'];
    }

    private function mapMechanic(string $type): string
    {
        return match ($type) {
            'multiple_choice' => 'riddle',
            'true_false' => 'binary_trap',
            default => 'standard_clash',
        };
    }
}
