<?php

namespace App\Tests\Service\Validation;

use App\Entity\Quiz;
use App\Service\Validation\QuizValidationManager;
use PHPUnit\Framework\TestCase;

class QuizValidationManagerTest extends TestCase
{
    private QuizValidationManager $manager;

    protected function setUp(): void
    {
        $this->manager = new QuizValidationManager();
    }

    public function testValidateWithValidQuiz(): void
    {
        $quiz = new Quiz();
        $quiz->setTitle('Final Exam');

        $errors = $this->manager->validate($quiz);
        $this->assertCount(0, $errors);
        $this->assertTrue($this->manager->isValid($quiz));
    }
}
