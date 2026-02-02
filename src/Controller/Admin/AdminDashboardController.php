<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }

    #[Route('/projects', name: 'admin_projects')]
    public function projects(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Projets']);
    }

    #[Route('/subscriptions', name: 'admin_subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Abonnements']);
    }

    #[Route('/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Utilisateurs']);
    }

    #[Route('/courses', name: 'admin_courses')]
    public function courses(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Cours']);
    }

    #[Route('/events', name: 'admin_events')]
    public function events(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Événements']);
    }

    #[Route('/opportunities', name: 'admin_opportunities')]
    public function opportunities(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Opportunités']);
    }

    #[Route('/applications', name: 'admin_applications')]
    public function applications(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Candidatures']);
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
