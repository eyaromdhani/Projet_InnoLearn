<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
<<<<<<< Updated upstream
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
=======
use App\Entity\Formulaire;
use App\Entity\Question;
use App\Entity\Cours;
use App\Form\FormulaireType;
use App\Form\QuestionType;
use App\Form\CoursType;
use App\Repository\FormulaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\CategorieCoursRepository;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;
use App\Repository\InscritEventRepository;
use App\Enum\StatutEvenementEnum;
use App\Entity\InscritEvent;
use App\Entity\User;
use App\Service\PdfService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
>>>>>>> Stashed changes

#[Route('/enseignant')]
class EnseignantController extends AbstractController
{
<<<<<<< Updated upstream
    #[Route('/dashboard', name: 'app_enseignant_dashboard')]
=======
    public function __construct(
        private PdfService $pdfService
    ) {}
    #[Route('/home', name: 'app_enseignant_home')]
>>>>>>> Stashed changes
    public function dashboard(): Response
    {
        // Mock data for the teacher dashboard
        $courses = [
            ['title' => 'UI/UX Design Masterclass', 'students' => 150, 'rating' => 4.8, 'color' => '#6366f1'],
            ['title' => 'Fullstack Web Development', 'students' => 85, 'rating' => 4.9, 'color' => '#a855f7'],
            ['title' => 'Advanced PHP & Symfony', 'students' => 42, 'rating' => 4.7, 'color' => '#f472b6'],
        ];

        // Category data (Dashboard specific)
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large', 'active' => true],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve', 'active' => false],
            ['name' => 'Développement', 'icon' => 'fa-code', 'active' => false],
            ['name' => 'Marketing', 'icon' => 'fa-bullhorn', 'active' => false],
            ['name' => 'Management', 'icon' => 'fa-tasks', 'active' => false],
            ['name' => 'IA', 'icon' => 'fa-robot', 'active' => false],
        ];

        return $this->render('enseignant/dashboard.html.twig', [
            'courses' => $courses,
            'categories' => $categories,
            'stats' => [
                'total_students' => 277,
                'active_courses' => 3,
                'avg_rating' => 4.8
            ]
        ]);
    }

    #[Route('/courses', name: 'app_enseignant_courses')]
<<<<<<< Updated upstream
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
            ['id' => 1, 'title' => 'UI/UX Design Masterclass', 'teacher' => 'Vous', 'price' => 49.99, 'rating' => 4.8, 'students' => 1250, 'category' => 'Design', 'image' => 'https://placehold.co/600x400/6366f1/white?text=UI/UX+Design'],
            ['id' => 2, 'title' => 'Full-Stack Web Dev with Symfony', 'teacher' => 'Vous', 'price' => 89.99, 'rating' => 4.9, 'students' => 850, 'category' => 'Développement', 'image' => 'https://placehold.co/600x400/a855f7/white?text=Symfony+Dev'],
            ['id' => 3, 'title' => 'Introduction to Python for AI', 'teacher' => 'Vous', 'price' => 59.99, 'rating' => 4.7, 'students' => 2100, 'category' => 'IA', 'image' => 'https://placehold.co/600x400/f472b6/white?text=Python+AI'],
        ];

        return $this->render('enseignant/courses.html.twig', [
            'categories' => $categories,
            'courses' => $courses,
=======
    public function courses(\App\Repository\CategorieCoursRepository $categorieCoursRepository): Response
    {
        // Fetch real categories from database
        $dbCategories = $categorieCoursRepository->findAll();

        // Transform to match template expectations for dropdown
        $categories = [['name' => 'Tout', 'icon' => 'fa-th-large', 'active' => true]];
        foreach ($dbCategories as $cat) {
            $categories[] = [
                'name' => $cat->getTitre(),
                'icon' => 'fa-tag',
                'active' => false
            ];
        }

        // Transform categories to display as cards
        $categoriesCards = [];
        $colors = ['#6366f1', '#a855f7', '#f472b6', '#ec4899', '#8b5cf6', '#06b6d4'];
        $colorIndex = 0;

        foreach ($dbCategories as $cat) {
            $categoriesCards[] = [
                'id' => $cat->getId(),
                'title' => $cat->getTitre(),
                'description' => $cat->getDescription(),
                'niveau' => $cat->getNiveau(),
                'category' => $cat->getTitre(),
                'image' => 'https://placehold.co/600x400/' . substr($colors[$colorIndex % count($colors)], 1) . '/white?text=' . urlencode($cat->getTitre()),
                'color' => $colors[$colorIndex % count($colors)]
            ];
            $colorIndex++;
        }

        return $this->render('enseignant/courses.html.twig', [
            'categories' => $categories,
            'courses' => $categoriesCards, // Display categories as cards
        ]);
    }

    #[Route('/category/{id}', name: 'app_enseignant_category_show', methods: ['GET', 'POST'])]
    public function categoryShow(int $id, CategorieCoursRepository $categorieCoursRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $categorieCoursRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $cours = new Cours();
        $cours->setCategorieCours($category);
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cours);
            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_category_show', ['id' => $category->getId()]);
        }

        return $this->render('enseignant/category_show.html.twig', [
            'category' => $category,
            'courses' => $category->getCours(),
            'form' => $form->createView(),
>>>>>>> Stashed changes
        ]);
    }

    #[Route('/projects', name: 'app_enseignant_projects')]
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
        ];

        return $this->render('enseignant/projects.html.twig', [
            'categories' => $categories,
            'projects' => $projects,
        ]);
    }

    #[Route('/events', name: 'app_enseignant_events')]
<<<<<<< Updated upstream
    public function events(): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Webinar', 'icon' => 'fa-video'],
            ['name' => 'Workshop', 'icon' => 'fa-tools'],
            ['name' => 'Networking', 'icon' => 'fa-users'],
        ];

        $events = [
            ['id' => 1, 'title' => 'The Future of AI in SaaS', 'category' => 'Webinar', 'date' => '2026-02-15', 'time' => '18:00', 'speaker' => 'Vous'],
            ['id' => 2, 'title' => 'Symfony Performance Workshop', 'category' => 'Workshop', 'date' => '2026-02-20', 'time' => '14:00', 'speaker' => 'Vous'],
        ];
=======
    public function events(EventRepository $eventRepository, InscritEventRepository $inscritEventRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = [
            ['value' => 'all', 'name' => 'Toutes les catégories', 'icon' => 'fa-th-large'],
            ['value' => 'conference', 'name' => 'Conférences', 'icon' => 'fa-chalkboard-teacher'],
            ['value' => 'workshop', 'name' => 'Workshops', 'icon' => 'fa-tools'],
            ['value' => 'hackaton', 'name' => 'Hackathons', 'icon' => 'fa-laptop-code'],
        ];

        $now = new \DateTime();
        $allEvents = $eventRepository->findAll();

        // Filter out cancelled events and auto-clean past events
        $events = [];
        foreach ($allEvents as $event) {
            // Skip cancelled events
            if ($event->getStatut() === StatutEvenementEnum::ANNULE) {
                continue;
            }

            // Auto-delete past terminated events
            if ($event->getStatut() === StatutEvenementEnum::TERMINE && $event->getDateFin() < $now) {
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
        $studentEmail = $session->get('enseignant_email'); // Use enseignant_email key
        $registrationStatuses = [];
        $eventCapacityStatus = [];

        if ($studentEmail) {
            $registrations = $inscritEventRepository->findBy(['email' => $studentEmail]);
            foreach ($registrations as $registration) {
                if ($registration->getEvent()) {
                    $registrationStatuses[$registration->getEvent()->getId()] = [
                        'status' => $registration->getStatus(),
                        'id' => $registration->getId()
                    ];
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
>>>>>>> Stashed changes

        return $this->render('enseignant/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
<<<<<<< Updated upstream
        ]);
    }

=======
            'registrationStatuses' => $registrationStatuses,
            'eventCapacityStatus' => $eventCapacityStatus,
        ]);
    }

    #[Route('/event/participate', name: 'enseignant_event_participate', methods: ['POST'])]
    public function participate(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, ValidatorInterface $validator): Response
    {
        $eventId = $request->request->get('event_id');
        $name = $request->request->get('name');
        $email = $request->request->get('email');

        if (!$eventId) {
            $this->addFlash('error', 'Événement non spécifié.');
            return $this->redirectToRoute('app_enseignant_events');
        }

        $event = $eventRepository->find($eventId);
        if (!$event) {
            $this->addFlash('error', 'Événement non trouvé.');
            return $this->redirectToRoute('app_enseignant_events');
        }

        $inscription = new InscritEvent();
        $inscription->setEvent($event);
        $inscription->setName($name ?? '');
        $inscription->setEmail($email ?? '');
        $inscription->setDateInscrit(new \DateTime());
        $inscription->setStatus('En attente');

        // Link the logged-in user
        $user = $this->getUser();
        if ($user instanceof User) {
            $inscription->setUser($user);
        }

        $errors = $validator->validate($inscription);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('app_enseignant_events');
        }

        $entityManager->persist($inscription);
        $entityManager->flush();

        // Save email to session to remember the user
        $request->getSession()->set('enseignant_email', $email);

        $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès.');
        return $this->redirectToRoute('app_enseignant_events');
    }

>>>>>>> Stashed changes
    #[Route('/stages', name: 'app_enseignant_stages')]
    public function stages(): Response
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

        return $this->render('enseignant/stages.html.twig', [
            'categories' => $categories,
            'jobs' => $jobs,
        ]);
    }

    #[Route('/books', name: 'app_enseignant_books')]
    public function books(\App\Repository\BookRepository $bookRepository): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Programmation', 'icon' => 'fa-code'],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve'],
            ['name' => 'Business', 'icon' => 'fa-chart-line'],
        ];

        return $this->render('enseignant/books.html.twig', [
            'categories' => $categories,
            'books' => $bookRepository->findAll(),
        ]);
    }
    #[Route('/quizzes', name: 'app_enseignant_quizzes')]
<<<<<<< Updated upstream
    public function quizzes(QuizRepository $quizRepository): Response
    {
        return $this->render('enseignant/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    #[Route('/quiz/new', name: 'app_enseignant_quiz_new', methods: ['GET', 'POST'])]
    public function addQuiz(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
=======
    public function quizzes(FormulaireRepository $formulaireRepository): Response
    {
        return $this->render('enseignant/formulaire/index.html.twig', [
            'formulaires' => $formulaireRepository->findAll(),
        ]);
    }

    #[Route('/quizzes/new', name: 'app_enseignant_quiz_new', methods: ['GET', 'POST'])]
    public function newQuiz(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);
>>>>>>> Stashed changes
            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_quizzes', [], Response::HTTP_SEE_OTHER);
        }

<<<<<<< Updated upstream
        return $this->render('enseignant/quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }
=======
        return $this->render('enseignant/formulaire/new.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quizzes/{id}/edit', name: 'app_enseignant_quiz_edit', methods: ['GET', 'POST'])]
    public function editQuiz(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_quizzes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enseignant/formulaire/edit.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quizzes/{id}', name: 'app_enseignant_quiz_delete', methods: ['POST'])]
    public function deleteQuiz(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formulaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formulaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_enseignant_quizzes', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/quizzes/{id}/questions', name: 'app_enseignant_quiz_questions', methods: ['GET', 'POST'])]
    public function manageQuestions(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        // Handle new question creation
        if ($request->isMethod('POST')) {
            $questionText = $request->request->get('question_text');
            $type = $request->request->get('type');
            $correctAnswer = $request->request->get('correct_answer');
            $points = $request->request->get('points');

            if ($questionText && $type && $correctAnswer && $points) {
                $question = new Question();
                $question->setQuestionText($questionText);
                $question->setType($type);
                $question->setCorrectAnswer($correctAnswer);
                $question->setPoints((int) $points);
                $question->setFormulaire($formulaire);

                $entityManager->persist($question);
                $entityManager->flush();

                $this->addFlash('success', 'Question ajoutée avec succès!');
                return $this->redirectToRoute('app_enseignant_quiz_questions', ['id' => $formulaire->getId()]);
            }
        }

        return $this->render('enseignant/formulaire/questions.html.twig', [
            'formulaire' => $formulaire,
            'questions' => $formulaire->getQuestions(),
        ]);
    }

    #[Route('/quizzes/question/{id}/edit', name: 'app_enseignant_question_edit', methods: ['GET', 'POST'])]
    public function editQuestion(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->remove('formulaire'); // We don't want to change the quiz here
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Question modifiée avec succès!');
            return $this->redirectToRoute('app_enseignant_quiz_questions', ['id' => $question->getFormulaire()->getId()]);
        }

        return $this->render('enseignant/formulaire/edit_question.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'formulaire' => $question->getFormulaire(),
        ]);
    }

    #[Route('/quizzes/question/{id}', name: 'app_enseignant_question_delete', methods: ['POST'])]
    public function deleteQuestion(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $formulaireId = $question->getFormulaire()->getId();
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
            $this->addFlash('success', 'Question supprimée!');
        }

        return $this->redirectToRoute('app_enseignant_quiz_questions', ['id' => $formulaireId]);
    }

    #[Route('/event/ticket/{id}', name: 'app_enseignant_event_ticket')]
    public function downloadTicket(InscritEvent $inscription): Response
    {
        $user = $this->getUser();
        $session = $this->container->get('request_stack')->getCurrentRequest()->getSession();
        $enseignantEmail = $session->get('enseignant_email');

        if ($inscription->getUser() !== $user && $inscription->getEmail() !== $enseignantEmail) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce ticket.');
        }

        if ($inscription->getStatus() !== 'Confirmé') {
            $this->addFlash('error', 'Le ticket n\'est pas encore disponible. L\'inscription doit être confirmée.');
            return $this->redirectToRoute('app_enseignant_events');
        }

        $pdfContent = $this->pdfService->generateEventTicket($inscription);
        $filename = preg_replace('/[^a-z0-9]+/', '-', strtolower($inscription->getEvent()->getTitre()));

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="ticket-' . $filename . '.pdf"',
            ]
        );
    }
>>>>>>> Stashed changes
}
