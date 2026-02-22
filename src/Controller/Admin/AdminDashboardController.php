<?php

namespace App\Controller\Admin;

<<<<<<< Updated upstream
=======
use App\Repository\OffreStageRepository;
>>>>>>> Stashed changes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Form\EventType;

use App\Enum\StatutInscriptionEnum;
use App\Enum\StatutEvenementEnum;

use App\Entity\InscritEvent;

use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
<<<<<<< Updated upstream
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig');
    }

    #[Route('/projects', name: 'admin_projects')]
    public function projects(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Projets']);
=======
    public function index(OffreStageRepository $offreStageRepository, \App\Repository\UserRepository $userRepository): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'recent_opportunities' => $offreStageRepository->findBy([], ['datePublication' => 'DESC'], 5),
            'recent_users' => $userRepository->findBy([], ['id' => 'DESC'], 5),
            'total_users' => $userRepository->count([])
        ]);
>>>>>>> Stashed changes
    }

    #[Route('/subscriptions', name: 'admin_subscriptions')]
    public function subscriptions(): Response
    {
        return $this->render('admin/dashboard/static.html.twig', ['title' => 'Abonnements']);
    }

<<<<<<< Updated upstream
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
    public function events(EventRepository $eventRepository, \App\Repository\InscritEventRepository $inscritEventRepository, EntityManagerInterface $entityManager): Response
    {
        $now = new \DateTime();
        $events = $eventRepository->findAll();
        
        // Auto-delete past events or cancelled events
        foreach ($events as $event) {
            if ($event->getStatut() === StatutEvenementEnum::ANNULE || 
                ($event->getStatut() === StatutEvenementEnum::TERMINE && $event->getDateFin() < $now)) {
                
                // Delete associated registrations first to avoid foreign key constraint
                $registrations = $inscritEventRepository->findBy(['event' => $event]);
                foreach ($registrations as $registration) {
                    $entityManager->remove($registration);
                }
                
                $entityManager->remove($event);
            }
        }
        $entityManager->flush();
        
        // Refetch after cleanup
        $events = $eventRepository->findAll();
        
        return $this->render('admin/dashboard/static.html.twig', [
            'title' => 'Événements',
            'events' => $events,
            'inscriptions' => $inscritEventRepository->findAll(),
        ]);
    }

    #[Route('/events/new', name: 'admin_event_new', methods: ['GET', 'POST'])]
    public function newEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('admin_events', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/events/{id}/change-status', name: 'admin_event_change_status', methods: ['POST'])]
    public function changeEventStatus(Event $event, Request $request, EntityManagerInterface $entityManager, \App\Repository\InscritEventRepository $inscritEventRepository): Response
    {
        $newStatus = $request->request->get('status');
        
        if ($this->isCsrfTokenValid('change-status'.$event->getId(), $request->request->get('_token'))) {
            // Map string to enum
            $statusEnum = match($newStatus) {
                'actif' => StatutEvenementEnum::ACTIF,
                'annule' => StatutEvenementEnum::ANNULE,
                'termine' => StatutEvenementEnum::TERMINE,
                default => null
            };
            
            if ($statusEnum) {
                $event->setStatut($statusEnum);
                $entityManager->flush();
                
                // If status is ANNULE, delete the event immediately
                if ($statusEnum === StatutEvenementEnum::ANNULE) {
                    // Delete associated registrations first to avoid foreign key constraint
                    $registrations = $inscritEventRepository->findBy(['event' => $event]);
                    foreach ($registrations as $registration) {
                        $entityManager->remove($registration);
                    }
                    
                    $entityManager->remove($event);
                    $entityManager->flush();
                }
            }
        }
        
        return $this->redirectToRoute('admin_events', [], Response::HTTP_SEE_OTHER);
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

=======
>>>>>>> Stashed changes
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

    #[Route('/inscriptions/{id}/delete', name: 'admin_inscription_delete', methods: ['POST'])]
    public function deleteInscritEvent(Request $request, InscritEvent $inscritEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscritEvent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscritEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_events', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/inscriptions/{id}/approve', name: 'admin_inscription_approve', methods: ['POST'])]
    public function approveInscritEvent(Request $request, InscritEvent $inscritEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$inscritEvent->getId(), $request->request->get('_token'))) {
            // Update InscritEvent status
            $inscritEvent->setStatus('Confirmé');

            // Update related Event status
            $event = $inscritEvent->getEvent();
            if ($event) {
                // Set Event status to ACTIF (Active)
                $event->setStatut(StatutEvenementEnum::ACTIF);
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_events', [], Response::HTTP_SEE_OTHER);
    }
}
