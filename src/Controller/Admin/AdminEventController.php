<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\InscritEvent;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\InscritEventRepository;
use App\Enum\StatutEvenementEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/event')]
class AdminEventController extends AbstractController
{
    #[Route('/', name: 'admin_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository, InscritEventRepository $inscritEventRepository): Response
    {
        return $this->render('admin/event/index.html.twig', [
            'events' => $eventRepository->findAll(),
            'inscriptions' => $inscritEventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('admin/event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/inscription/{id}/delete', name: 'admin_event_delete_inscription', methods: ['POST'])]
    public function deleteInscription(Request $request, InscritEvent $inscritEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inscritEvent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscritEvent);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('admin_event_index') . '#inscriptions');
    }

    #[Route('/inscription/{id}/confirm', name: 'admin_event_confirm_inscription', methods: ['POST'])]
    public function confirmInscription(Request $request, InscritEvent $inscritEvent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('confirm' . $inscritEvent->getId(), $request->request->get('_token'))) {
            $inscritEvent->setStatus('Confirmé');
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('admin_event_index') . '#inscriptions');
    }
}
