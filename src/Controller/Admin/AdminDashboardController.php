<?php

namespace App\Controller\Admin;

use App\Repository\OffreStageRepository;
use App\Repository\UserRepository;
use App\Repository\CoursRepository;
use App\Repository\ProjectRepository;
use App\Repository\QuizRepository;
use App\Repository\CategorieCoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(
        OffreStageRepository $offreStageRepository,
        UserRepository $userRepository,
        CoursRepository $coursRepository,
        ProjectRepository $projectRepository,
        QuizRepository $quizRepository,
        CategorieCoursRepository $categorieCoursRepository
    ): Response
    {
        // Statistics avancées
        $stats = [
            'total_users' => $userRepository->count([]),
            'active_users' => $userRepository->count(['isBanned' => false]),
            'total_courses' => $coursRepository->count([]),
            'total_projects' => $projectRepository->count([]),
            'total_quizzes' => $quizRepository->count([]),
            'total_categories' => $categorieCoursRepository->count([]),
            'total_opportunities' => $offreStageRepository->count([]),
            'open_opportunities' => $offreStageRepository->count(['statut' => 'ouverte']),
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'recent_opportunities' => $offreStageRepository->findBy([], ['datePublication' => 'DESC'], 5),
            'recent_users' => $userRepository->findBy([], ['id' => 'DESC'], 5),
            'total_users' => $userRepository->count([]),
            'stats' => $stats,
        ]);
    }

    #[Route('/subscriptions', name: 'admin_subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Abonnements']);
    }

    #[Route('/security-legacy', name: 'admin_security')]
    public function security(): Response
    {
        return $this->redirectToRoute('admin_security_report');
    }


    #[Route('/stats-advanced', name: 'admin_stats_advanced')]
    public function statsAdvanced(): Response
    {
        return $this->redirectToRoute('admin_verify_access');
    }

    #[Route('/settings', name: 'admin_settings')]
    public function settings(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Paramètres']);
    }
}
