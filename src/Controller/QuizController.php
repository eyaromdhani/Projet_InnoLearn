<?php

namespace App\Controller;

use App\Entity\QuizResponse;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/test')]
#[IsGranted('ROLE_USER')]
class QuizController extends AbstractController
{
    #[Route('/profiling', name: 'app_test_profiling')]
    #[Route('/profiling', name: 'app_quiz_profiling_legacy')]
    public function profiling(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        
        if ($user->getUserProfile() && $user->getUserProfile()->getQuizCompletedAt()) {
            return $this->redirectToRoute('app_home');
        }

        // Questions for Learning Style & Personality Profiling
        // These would normally come from a repository/DB, but we'll define them here for now
        $questions = [
            [
                'id' => 1,
                'text' => 'When learning something new, do you prefer:',
                'options' => [
                    ['value' => 'visual', 'text' => 'Watching a video or looking at diagrams'],
                    ['value' => 'auditory', 'text' => 'Listening to a tutorial or podcast'],
                    ['value' => 'reading', 'text' => 'Reading an article or documentation'],
                    ['value' => 'kinesthetic', 'text' => 'Trying it out immediately with interactive exercises'],
                ]
            ],
            [
                'id' => 2,
                'text' => 'How do you usually approach a large project?',
                'options' => [
                    ['value' => 'disciplined', 'text' => 'Break it down and start immediately'],
                    ['value' => 'procrastinator', 'text' => 'Wait until the deadline is near to get started'],
                    ['value' => 'perfectionist', 'text' => 'Spend a lot of time planning every detail before starting'],
                    ['value' => 'anxious', 'text' => 'Feel overwhelmed and struggle to start'],
                ]
            ],
            [
                'id' => 3,
                'text' => 'In a classroom or workshop, you are most likely to:',
                'options' => [
                    ['value' => 'visual', 'text' => 'Take notes with colors and drawings'],
                    ['value' => 'auditory', 'text' => 'Ask questions and participate in discussions'],
                    ['value' => 'reading', 'text' => 'Follow along with the handout or book'],
                    ['value' => 'kinesthetic', 'text' => 'Diddle or try to find a way to practice the concepts'],
                ]
            ],
            // ... more questions would be added here (Target 8-12)
        ];

        return $this->render('test/profiling.html.twig', [
            'questions' => $questions,
        ]);
    }

    #[Route('/profiling/submit', name: 'app_test_profiling_submit', methods: ['POST'])]
    #[Route('/profiling/submit', name: 'app_quiz_profiling_submit_legacy', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);
        
        if (!$data || !isset($data['responses'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Bulk delete old responses for efficiency and to prevent timeouts
            $em->createQuery('DELETE FROM App\Entity\QuizResponse qr WHERE qr.user = :user')
               ->setParameter('user', $user)
               ->execute();

            // Save new responses
            foreach ($data['responses'] as $questionId => $answerValue) {
                $qResponse = new QuizResponse();
                $qResponse->setUser($user);
                $qResponse->setQuestionId((int)$questionId);
                $qResponse->setAnswerValue($answerValue);
                // QuizResponse __construct handles createdAt (\DateTimeImmutable)
                $em->persist($qResponse);
            }

            // Create or Update UserProfile
            $profile = $user->getUserProfile() ?? new UserProfile();
            $profile->setUser($user);
            // Ensure compatibility with setQuizCompletedAt
            $profile->setQuizCompletedAt(new \DateTime());
            
            $styleCounts = ['visual' => 0, 'auditory' => 0, 'reading' => 0, 'kinesthetic' => 0];
            $personalityTags = [];
            
            foreach ($data['responses'] as $val) {
                if (isset($styleCounts[$val])) {
                    $styleCounts[$val]++;
                } else {
                    $personalityTags[] = $val;
                }
            }
            
            arsort($styleCounts);
            $learningStyle = array_key_first($styleCounts) ?: 'visual';
            
            $profile->setLearningStyle($learningStyle);
            $profile->setPersonalityType($personalityTags);
            
            $em->persist($profile);
            $em->flush();

            return new JsonResponse([
                'status' => 'success',
                'learning_style' => $learningStyle,
                'personality_type' => $personalityTags
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
