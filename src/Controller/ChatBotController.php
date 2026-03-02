<?php

namespace App\Controller;

use App\Service\ChatBotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatBotController extends AbstractController
{
    #[Route('/chatbot/ask', name: 'app_chatbot_ask', methods: ['POST'])]
    public function ask(Request $request, ChatBotService $chatBotService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        if (empty($userMessage)) {
            return new JsonResponse(['response' => 'Désolé, je n\'ai pas reçu ton message.'], 400);
        }

        $botResponse = $chatBotService->generateResponse($userMessage);

        return new JsonResponse([
            'response' => $botResponse,
            'time' => date('H:i')
        ]);
    }
}
