<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\User;
use App\Repository\GameProgressRepository;
use App\Service\StoryGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/game')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $avatar = $user->getGameAvatar();

        if (!$avatar) {
            return $this->redirectToRoute('app_avatar_studio'); // Redirect to studio to create avatar
        }

        return $this->render('game/world.html.twig', [
            'avatar' => $avatar,
        ]);
    }

    #[Route('/load-zone/{courseId}', name: 'api_game_load_zone', methods: ['GET'])]
    public function loadZone(int $courseId, EntityManagerInterface $em, StoryGeneratorService $storyService): JsonResponse
    {
        $course = $em->getRepository(Cours::class)->find($courseId);
        if (!$course) {
            return new JsonResponse(['error' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
             return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $story = $storyService->generateStory($course, $user->getUserProfile());

        return new JsonResponse([
            'course' => [
                'id' => $course->getId(),
                'nom' => $course->getNom(),
                'description' => $course->getDescription(),
            ],
            'story' => $story,
            'assets' => [
                'environment' => $story['environment_type'] . '.glb'
            ]
        ]);
    }
}
