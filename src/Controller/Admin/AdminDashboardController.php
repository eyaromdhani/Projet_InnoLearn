<?php

namespace App\Controller\Admin;

use App\Repository\OffreStageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(OffreStageRepository $offreStageRepository, \App\Repository\UserRepository $userRepository): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'recent_opportunities' => $offreStageRepository->findBy([], ['datePublication' => 'DESC'], 5),
            'recent_users' => $userRepository->findBy([], ['id' => 'DESC'], 5),
            'total_users' => $userRepository->count([])
        ]);
    }

    #[Route('/subscriptions', name: 'admin_subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Abonnements']);
    }

    #[Route('/reports', name: 'admin_reports')]
    public function reports(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Rapports']);
    }

    #[Route('/settings', name: 'admin_settings')]
    public function settings(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Paramètres']);
    }
}
