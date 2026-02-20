<?php

namespace App\Controller;

use App\Service\AIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/enseignant/ai')]
class AIController extends AbstractController
{
    #[Route('/generate-questions', name: 'app_ai_generate_questions', methods: ['POST'])]
    public function generate(Request $request, AIService $aiService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        if (empty($text)) {
            return new JsonResponse(['error' => 'Texte manquant'], 400);
        }

        try {
            $questions = $aiService->generateQuestionsFromText($text);
            return new JsonResponse($questions);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
