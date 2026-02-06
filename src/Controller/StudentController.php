<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\EventRepository;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/dashboard', name: 'app_student_dashboard')]
    public function dashboard(): Response
    {
        // Mock data for the dashboard
        $courses = [
            ['title' => 'UI/UX Design Masterclass', 'teacher' => 'John Doe', 'progress' => 75, 'color' => '#6366f1'],
            ['title' => 'Fullstack Web Development', 'teacher' => 'Jane Smith', 'progress' => 40, 'color' => '#a855f7'],
            ['title' => 'Advanced PHP & Symfony', 'teacher' => 'Alex Johnson', 'progress' => 10, 'color' => '#f472b6'],
        ];

        // Category data
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large', 'active' => true],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve', 'active' => false],
            ['name' => 'Développement', 'icon' => 'fa-code', 'active' => false],
            ['name' => 'Marketing', 'icon' => 'fa-bullhorn', 'active' => false],
            ['name' => 'Management', 'icon' => 'fa-tasks', 'active' => false],
            ['name' => 'IA', 'icon' => 'fa-robot', 'active' => false],
        ];

        return $this->render('student/dashboard.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'stats' => [
                'hours' => 124,
                'courses' => 3,
                'certificates' => 5
            ]
        ]);
    }

    #[Route('/courses', name: 'app_student_courses')]
    public function courses(): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large', 'active' => true],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve', 'active' => false],
            ['name' => 'Développement', 'icon' => 'fa-code', 'active' => false],
            ['name' => 'Marketing', 'icon' => 'fa-bullhorn', 'active' => false],
            ['name' => 'Management', 'icon' => 'fa-tasks', 'active' => false],
            ['name' => 'IA', 'icon' => 'fa-robot', 'active' => false],
        ];

        $courses = [
            ['id' => 1, 'title' => 'UI/UX Design Masterclass', 'teacher' => 'Sarah Connor', 'price' => 49.99, 'rating' => 4.8, 'students' => 1250, 'category' => 'Design', 'image' => 'https://placehold.co/600x400/6366f1/white?text=UI/UX+Design'],
            ['id' => 2, 'title' => 'Full-Stack Web Dev with Symfony', 'teacher' => 'Alex Johnson', 'price' => 89.99, 'rating' => 4.9, 'students' => 850, 'category' => 'Développement', 'image' => 'https://placehold.co/600x400/a855f7/white?text=Symfony+Dev'],
            ['id' => 3, 'title' => 'Introduction to Python for AI', 'teacher' => 'Michael Reeds', 'price' => 59.99, 'rating' => 4.7, 'students' => 2100, 'category' => 'IA', 'image' => 'https://placehold.co/600x400/f472b6/white?text=Python+AI'],
            ['id' => 4, 'title' => 'Modern Marketing Strategies', 'teacher' => 'Emma Watson', 'price' => 39.99, 'rating' => 4.6, 'students' => 1500, 'category' => 'Marketing', 'image' => 'https://placehold.co/600x400/fbbf24/white?text=Marketing'],
            ['id' => 5, 'title' => 'Advanced React patterns', 'teacher' => 'John Doe', 'price' => 69.99, 'rating' => 4.9, 'students' => 980, 'category' => 'Développement', 'image' => 'https://placehold.co/600x400/0ea5e9/white?text=React+Advanced'],
            ['id' => 6, 'title' => 'Brand Identity Design', 'teacher' => 'Jane Smith', 'price' => 44.99, 'rating' => 4.8, 'students' => 740, 'category' => 'Design', 'image' => 'https://placehold.co/600x400/8b5cf6/white?text=Brand+Design'],
        ];

        return $this->render('student/courses.html.twig', [
            'categories' => $categories,
            'courses' => $courses,
        ]);
    }

    #[Route('/projects', name: 'app_student_projects')]
    public function projects(): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'IA', 'icon' => 'fa-robot'],
            ['name' => 'Web', 'icon' => 'fa-globe'],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve'],
        ];

        $projects = [
            ['id' => 1, 'title' => 'Eco-Track Mobile App', 'category' => 'Web', 'lead' => 'Emma Watson', 'members' => 3, 'max_members' => 5, 'status' => 'En cours'],
            ['id' => 2, 'title' => 'AI Chatbot for Education', 'category' => 'IA', 'lead' => 'John Doe', 'members' => 2, 'max_members' => 4, 'status' => 'Recherche membres'],
            ['id' => 3, 'title' => 'Branding InnoLearn 2026', 'category' => 'Design', 'lead' => 'Sophie Martin', 'members' => 5, 'max_members' => 5, 'status' => 'Complet'],
        ];

        return $this->render('student/projects.html.twig', [
            'categories' => $categories,
            'projects' => $projects,
        ]);
    }

    #[Route('/certificates', name: 'app_student_certificates')]
    public function certificates(): Response
    {
        $certificates = [
            ['id' => 1, 'title' => 'Fullstack Web Developer', 'date' => '2025-12-15', 'issuer' => 'InnoLearn Academy', 'image' => 'https://placehold.co/600x400/6366f1/white?text=Fullstack+Dev'],
            ['id' => 2, 'title' => 'Mastering AI Ethics', 'date' => '2026-01-10', 'issuer' => 'Google Cloud', 'image' => 'https://placehold.co/600x400/a855f7/white?text=AI+Ethics'],
        ];

        return $this->render('student/certificates.html.twig', [
            'certificates' => $certificates,
        ]);
    }

    #[Route('/events', name: 'app_student_events')]
    public function events(EventRepository $eventRepository, \App\Repository\InscritEventRepository $inscritEventRepository, \Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Webinar', 'icon' => 'fa-video'],
            ['name' => 'Workshop', 'icon' => 'fa-tools'],
            ['name' => 'Networking', 'icon' => 'fa-users'],
        ];

        $now = new \DateTime();
        $allEvents = $eventRepository->findAll();
        
        // Filter out cancelled events and auto-clean past events
        $events = [];
        foreach ($allEvents as $event) {
            // Skip cancelled events
            if ($event->getStatut() === \App\Enum\StatutEvenementEnum::ANNULE) {
                continue;
            }
            
            // Auto-delete past terminated events
            if ($event->getStatut() === \App\Enum\StatutEvenementEnum::TERMINE && $event->getDateFin() < $now) {
                // Delete associated registrations first to avoid foreign key constraint
                $registrations = $inscritEventRepository->findBy(['event' => $event]);
                foreach ($registrations as $registration) {
                    $entityManager->remove($registration);
                }
                
                $entityManager->remove($event);
                continue;
            }
            
            $events[] = $event;
        }
        $entityManager->flush();
        
        // Fetch registration statuses for the current session user
        $session = $request->getSession();
        $studentEmail = $session->get('student_email');
        $registrationStatuses = [];
        $eventCapacityStatus = [];

        if ($studentEmail) {
            $registrations = $inscritEventRepository->findBy(['email' => $studentEmail]);
            foreach ($registrations as $registration) {
                if ($registration->getEvent()) {
                    $registrationStatuses[$registration->getEvent()->getId()] = $registration->getStatus();
                }
            }
        }
        
        // Calculate capacity status for each event
        foreach ($events as $event) {
            $confirmedCount = $inscritEventRepository->count([
                'event' => $event,
                'status' => 'Confirmé'
            ]);
            
            $eventCapacityStatus[$event->getId()] = [
                'isFull' => $confirmedCount >= $event->getCapacite(),
                'confirmed' => $confirmedCount,
                'capacity' => $event->getCapacite()
            ];
        }

        return $this->render('student/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
            'registrationStatuses' => $registrationStatuses,
            'eventCapacityStatus' => $eventCapacityStatus,
        ]);
    }

    #[Route('/career', name: 'app_student_career')]
    public function career(): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Internship', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Full-time', 'icon' => 'fa-briefcase'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop-code'],
        ];

        $jobs = [
            ['id' => 1, 'title' => 'Junior Symfony Developer', 'company' => 'SensioLabs', 'category' => 'Full-time', 'location' => 'Paris', 'salary' => '45k-50k'],
            ['id' => 2, 'title' => 'UX Design Intern', 'company' => 'Adobe', 'category' => 'Internship', 'location' => 'Remote', 'salary' => '1.5k/month'],
        ];

        return $this->render('student/career.html.twig', [
            'categories' => $categories,
            'jobs' => $jobs,
        ]);
    }

    #[Route('/books', name: 'app_student_books')]
    public function books(\App\Repository\BookRepository $bookRepository): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Programmation', 'icon' => 'fa-code'],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve'],
            ['name' => 'Business', 'icon' => 'fa-chart-line'],
        ];

        return $this->render('student/books.html.twig', [
            'categories' => $categories,
            'books' => $bookRepository->findAll(),
        ]);
    }
    #[Route('/quizzes', name: 'app_student_quizzes')]
    public function quizzes(QuizRepository $quizRepository): Response
    {
        return $this->render('student/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/event/participate', name: 'student_event_participate', methods: ['POST'])]
    public function participate(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $eventId = $request->request->get('event_id');
        $name = $request->request->get('name');
        $email = $request->request->get('email');

        if (!$eventId || !$name || !$email) {
            $this->addFlash('error', 'Tous les champs sont requis.');
            return $this->redirectToRoute('app_student_events');
        }

        $event = $eventRepository->find($eventId);
        if (!$event) {
            $this->addFlash('error', 'Événement non trouvé.');
            return $this->redirectToRoute('app_student_events');
        }

        $inscription = new \App\Entity\InscritEvent();
        $inscription->setEvent($event);
        $inscription->setName($name);
        $inscription->setEmail($email);
        $inscription->setDateInscrit(new \DateTime());
        $inscription->setStatus('En attente');
        
        $entityManager->persist($inscription);
        $entityManager->flush();

        // Save email to session to remember the user
        $request->getSession()->set('student_email', $email);

        $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès.');
        return $this->redirectToRoute('app_student_events');
    }
}
