<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\EventRepository;
use App\Repository\InscritEventRepository;
use App\Enum\StatutEvenementEnum;
use App\Entity\InscritEvent;
use App\Entity\User;
use App\Service\PdfService;
use App\Repository\QuizResultRepository;
use App\Service\AIService;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/enseignant')]
class EnseignantController extends AbstractController
{
    public function __construct(
        private PdfService $pdfService
    ) {
    }

    #[Route('/home', name: 'app_enseignant_home')]
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
            ['name' => 'D&eacute;veloppement', 'icon' => 'fa-code', 'active' => false],
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
                'image' => 'https://placehold.co/600x400/' . substr($colors[$colorIndex % count($colors)], 1) . '/white?text=' . urlencode((string) $cat->getTitre()),
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
        ]);
    }

    #[Route('/projects', name: 'app_enseignant_projects')]
    public function projects(\App\Repository\ProjectRepository $projectRepository): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'IA', 'icon' => 'fa-robot'],
            ['name' => 'Web', 'icon' => 'fa-globe'],
            ['name' => 'Design', 'icon' => 'fa-bezier-curve'],
        ];

        return $this->render('enseignant/projects.html.twig', [
            'categories' => $categories,
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/events', name: 'app_enseignant_events')]
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
                    $registrationStatuses[$registration->getEvent()->getId()] = $registration;
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

        return $this->render('enseignant/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
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
        $inscription->setDateInscrit(new \DateTime());
        $inscription->setStatus('En attente');

        // Link the logged-in user and auto-populate their data
        $user = $this->getUser();
        if ($user instanceof User) {
            $inscription->setUser($user);
            // Auto-populate name and email from logged-in user
            $inscription->setName((string) ($user->getName() ?? ''));
            $inscription->setEmail((string) $user->getEmail());
        } else {
            // Guest user - use provided data
            $inscription->setName((string) ($name ?? ''));
            $inscription->setEmail((string) ($email ?? ''));
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
    public function quizzes(FormulaireRepository $formulaireRepository, QuizResultRepository $quizResultRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $formulaires = $formulaireRepository->findAll();
        $formulairesData = [];

        foreach ($formulaires as $form) {
            $stats = $quizResultRepository->findStatsByFormulaire($form->getId());

            // Create a small Chart for each quiz
            $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
            $chart->setData([
                'labels' => ['Réussis', 'Échecs'],
                'datasets' => [
                    [
                        'backgroundColor' => ['#22c55e', '#ef4444'],
                        'borderColor' => ['#ffffff', '#ffffff'],
                        'borderWidth' => 2,
                        'data' => [$stats['pass'], $stats['fail']],
                        'hoverBackgroundColor' => ['#16a34a', '#dc2626'],
                        'hoverBorderWidth' => 3,
                    ],
                ],
            ]);
            $chart->setOptions([
                'responsive' => true,
                'maintainAspectRatio' => true,
                'plugins' => [
                    'legend' => ['display' => false],
                    'tooltip' => [
                        'enabled' => true,
                        'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                        'padding' => 12,
                        'cornerRadius' => 8,
                        'titleFont' => ['size' => 14, 'weight' => 'bold'],
                        'bodyFont' => ['size' => 13],
                        'displayColors' => true,
                    ],
                ],
                'cutout' => '65%',
                'animation' => [
                    'animateRotate' => true,
                    'animateScale' => true,
                    'duration' => 1000,
                    'easing' => 'easeOutQuart',
                ],
            ]);

            $formulairesData[] = [
                'entity' => $form,
                'stats' => $stats,
                'chart' => $chart
            ];
        }

        return $this->render('enseignant/formulaire/index.html.twig', [
            'formulaires' => $formulairesData,
        ]);
    }

    #[Route('/quizzes/{id}/analysis', name: 'app_enseignant_quiz_analysis')]
    public function analyzeQuiz(
        int $id,
        QuizResultRepository $quizResultRepository,
        FormulaireRepository $formulaireRepository,
        AIService $aiService
    ): Response {
        $form = $formulaireRepository->find($id);
        if (!$form) {
            return $this->json(['error' => 'Quiz introuvable'], 404);
        }

        $stats = $quizResultRepository->findStatsByFormulaire($id);
        $totalResults = $stats['pass'] + $stats['fail'];

        if ($totalResults === 0) {
            return $this->json(['analysis' => "Aucun résultat n'est encore disponible pour ce quiz. Les conseils de l'IA apparaîtront une fois que des étudiants auront participé."]);
        }

        $analysis = $aiService->getPedagogicalAnalysis(
            $form->getTitre(),
            $stats,
            count($form->getQuestions())
        );

        return $this->json(['analysis' => $analysis]);
    }

    #[Route('/quizzes/courses-by-category/{id}', name: 'app_enseignant_courses_by_category')]
    public function getCoursesByCategory(int $id, CoursRepository $coursRepository): Response
    {
        $categoryCourses = $coursRepository->findBy(['categorieCours' => $id]);
        $data = [];
        foreach ($categoryCourses as $course) {
            $data[] = [
                'id' => $course->getId(),
                'nom' => $course->getNom()
            ];
        }
        return $this->json($data);
    }

    #[Route('/quizzes/new', name: 'app_enseignant_quiz_new', methods: ['GET', 'POST'])]
    public function newQuiz(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);

            // Sync bidirectional relation
            if ($formulaire->getCours()) {
                $formulaire->getCours()->setQuiz($formulaire);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_quizzes', [], Response::HTTP_SEE_OTHER);
        }

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
            // Sync bidirectional relation
            if ($formulaire->getCours()) {
                $formulaire->getCours()->setQuiz($formulaire);
            }

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
    public function downloadTicket(InscritEvent $inscription, Request $request): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();
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

    #[Route('/course/edit/{id}', name: 'app_enseignant_course_edit', methods: ['GET', 'POST'])]
    public function editCourse(int $id, CoursRepository $coursRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = $coursRepository->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        $form = $this->createForm(CoursType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le cours a été modifié avec succès !');
            return $this->redirectToRoute('app_enseignant_category_show', ['id' => $course->getCategorieCours()->getId()]);
        }

        return $this->render('enseignant/category_show.html.twig', [
            'category' => $course->getCategorieCours(),
            'courses' => $course->getCategorieCours()->getCours(),
            'form' => $form->createView(),
            'edit_mode' => true,
            'edit_course_id' => $id
        ]);
    }

    #[Route('/course/delete/{id}', name: 'app_enseignant_course_delete', methods: ['POST'])]
    public function deleteCourse(int $id, CoursRepository $coursRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $course = $coursRepository->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        $categoryId = $course->getCategorieCours()->getId();

        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
            $this->addFlash('success', 'Le cours a été supprimé avec succès !');
        }

        return $this->redirectToRoute('app_enseignant_category_show', ['id' => $categoryId]);
    }
}
