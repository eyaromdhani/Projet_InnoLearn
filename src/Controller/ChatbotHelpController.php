<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChatbotHelpController extends AbstractController
{
    #[Route('/chatbot/help', name: 'app_chatbot_help', methods: ['GET'])]
    public function help(): Response
    {
        return $this->render('chatbot/help.html.twig');
    }
}
