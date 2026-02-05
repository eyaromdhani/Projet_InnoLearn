<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\FormulaireRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

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
    public function events(): Response
    {
        $categories = [
            ['name' => 'Tout', 'icon' => 'fa-th-large'],
            ['name' => 'Webinar', 'icon' => 'fa-video'],
            ['name' => 'Workshop', 'icon' => 'fa-tools'],
            ['name' => 'Networking', 'icon' => 'fa-users'],
        ];

        $events = [
            ['id' => 1, 'title' => 'The Future of AI in SaaS', 'category' => 'Webinar', 'date' => '2026-02-15', 'time' => '18:00', 'speaker' => 'Bill Gates'],
            ['id' => 2, 'title' => 'Symfony Performance Workshop', 'category' => 'Workshop', 'date' => '2026-02-20', 'time' => '14:00', 'speaker' => 'Fabien Potencier'],
        ];

        return $this->render('student/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
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
    public function quizzes(QuizRepository $quizRepository, \App\Repository\FormulaireRepository $formulaireRepository): Response
    {
        return $this->render('student/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
            'formulaires' => $formulaireRepository->findAll(),
        ]);
    }
    
    #[Route('/formulaires', name: 'app_student_formulaires')]
    public function formulaires(\App\Repository\FormulaireRepository $formulaireRepository): Response
    {
        return $this->render('student/formulaire/index.html.twig', [
            'formulaires' => $formulaireRepository->findAll(),
        ]);
    }
    
    #[Route('/formulaire/{id}', name: 'app_student_take_formulaire', methods: ['GET', 'POST'])]
    public function takeFormulaire(Request $request, \App\Entity\Formulaire $formulaire): Response
    {
        if ($request->isMethod('POST')) {
            $questions = $formulaire->getQuestions();
            $totalPoints = 0;
            $userScore = 0;
            $results = [];
            $submittedAnswers = $request->request->all('answers');

            foreach ($questions as $question) {
                $totalPoints += $question->getPoints();
                $userAnswer = $submittedAnswers[$question->getId()] ?? null;
                $correctAnswer = $question->getCorrectAnswer();

                $isCorrect = false;
                if ($userAnswer !== null) {
                    $uAns = trim(strtolower($userAnswer));
                    $cAns = trim(strtolower($correctAnswer));
                    
                    // Boolean normalization
                    if ($question->getType() === 'true_false') {
                        $uAns = ($uAns === 'true' || $uAns === '1' || $uAns === 'vrai') ? '1' : '0';
                        $cAns = ($cAns === 'true' || $cAns === '1' || $cAns === 'vrai') ? '1' : '0';
                    }
                    
                    if ($question->getType() === 'number') {
                        if (is_numeric($uAns) && is_numeric($cAns)) {
                            if ((float)$uAns === (float)$cAns) {
                                $userScore += $question->getPoints();
                                $isCorrect = true;
                            }
                        }
                    } else {
                        if ($uAns === $cAns) {
                            $userScore += $question->getPoints();
                            $isCorrect = true;
                        }
                    }
                }

                $results[] = [
                    'question' => $question,
                    'userAnswer' => $userAnswer,
                    'isCorrect' => $isCorrect,
                    'rawSubmitted' => $userAnswer,
                    'expected' => $correctAnswer
                ];
            }

            $lastResult = [
                'formulaire_id' => $formulaire->getId(),
                'score' => $userScore,
                'total' => $totalPoints,
                'results' => $results,
                'percentage' => $totalPoints > 0 ? round(($userScore / $totalPoints) * 100) : 0,
            ];
            $request->getSession()->set('last_quiz_result', $lastResult);

            return $this->render('student/formulaire/results.html.twig', $lastResult + ['formulaire' => $formulaire]);
        }

        return $this->render('student/formulaire/take.html.twig', [
            'formulaire' => $formulaire,
            'questions' => $formulaire->getQuestions(),
        ]);
    }

    #[Route('/formulaire/{id}/pdf', name: 'app_student_formulaire_pdf')]
    public function generatePdf(\App\Entity\Formulaire $formulaire, Request $request): Response
    {
        $lastResult = $request->getSession()->get('last_quiz_result');

        if (!$lastResult || $lastResult['formulaire_id'] !== $formulaire->getId()) {
            $this->addFlash('error', 'Impossible de générer le PDF. Veuillez repasser le quiz.');
            return $this->redirectToRoute('app_student_take_formulaire', ['id' => $formulaire->getId()]);
        }

        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('student/formulaire/results_pdf.html.twig', [
            'formulaire' => $formulaire,
            'score' => $lastResult['score'],
            'total' => $lastResult['total'],
            'results' => $lastResult['results'],
            'percentage' => $lastResult['percentage']
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="resultats_quiz_' . $formulaire->getId() . '.pdf"'
        ]);
    }
}
