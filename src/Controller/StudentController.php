<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\FormulaireRepository;
use App\Repository\ProjectRepository;        // <-- MISSING
use App\Repository\DepotRepository;          // <-- MISSING
use Doctrine\ORM\EntityManagerInterface;     // <-- Needed later
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Depot;                        // <-- Needed
use App\Form\DepotType;                      // <-- Needed

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
    public function projects(ProjectRepository $projectRepository): Response
    {
        // Récupérer tous les projets depuis la base de données
        $projects = $projectRepository->findAll();
        
        // Transformer les entités en tableaux pour le template
        $projectsData = [];
        foreach ($projects as $project) {
            $projectsData[] = [
                'id' => $project->getId(),
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'status' => $this->getDisplayStatus($project->getStatus()),
                'start_date' => $project->getStartDate()->format('d/m/Y'),
                'end_date' => $project->getEndDate() ? $project->getEndDate()->format('d/m/Y') : null,
                'created_at' => $project->getCreatedAt()->format('d/m/Y'),
                // Champs avec valeurs fixes (non présents dans votre entité)
                'category' => 'Développement',
                'lead' => 'Équipe InnoLearn',
                'members' => 1,
                'max_members' => 3,
                'technologies' => ['Symfony', 'PHP', 'Twig'],
                'difficulty' => 'Intermédiaire',
                'duration' => $this->calculateDuration($project)
            ];
        }

        // Catégories pour le filtre
        $categories = [
            ['name' => 'AI', 'icon' => 'fa-robot'],
            ['name' => 'Web', 'icon' => 'fa-code'],
            ['name' => 'Design', 'icon' => 'fa-paint-brush']
        ];

        return $this->render('student/projects.html.twig', [
            'projects' => $projectsData,
            'categories' => $categories,
        ]);
    }

    #[Route('/projects/{id}/detail', name: 'app_student_project_detail', methods: ['GET'])]
    public function projectDetail(int $id, ProjectRepository $projectRepository, DepotRepository $depotRepository): Response
    {
        $project = $projectRepository->find($id);
        
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }
        
        // Transformer l'entité en tableau pour le template
        $projectData = [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'status' => $this->getDisplayStatus($project->getStatus()),
            'start_date' => $project->getStartDate()->format('d/m/Y'),
            'end_date' => $project->getEndDate() ? $project->getEndDate()->format('d/m/Y') : null,
            'created_at' => $project->getCreatedAt()->format('d/m/Y'),
            // Champs avec valeurs fixes (non présents dans votre entité)
            'category' => 'Développement',
            'lead' => 'Équipe InnoLearn',
            'members' => 1,
            'max_members' => 3,
            'technologies' => ['Symfony', 'PHP', 'Twig'],
            'difficulty' => 'Intermédiaire',
            'duration' => $this->calculateDuration($project)
        ];
        
        // Récupérer les dépôts pour ce projet
        $depots = $depotRepository->findBy(['project' => $project]);
        
        return $this->render('student/project_detail.html.twig', [
            'project' => $projectData,
            'depots' => $depots,
        ]);
    }

#[Route('/projects/{id}/depot/new', name: 'app_student_depot_new', methods: ['GET', 'POST'])]
public function newDepot(Request $request, int $id, ProjectRepository $projectRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
{
    $project = $projectRepository->find($id);
    
    if (!$project) {
        throw $this->createNotFoundException('Projet non trouvé');
    }
    
    $depot = new Depot();
    $depot->setProject($project);
    
    // Remplir automatiquement le nom de l'étudiant
    $depot->setStudentName($this->getUser() ? $this->getUser()->getFullName() : 'Étudiant InnoLearn');
    
    $form = $this->createForm(DepotType::class, $depot, [
        'is_edit' => false
    ]);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted()) {
        // Validation SIMPLE - seulement pour type et description
        $errors = $validator->validate($depot, null, ['Default']);
        
        // Validation du fichier (basique)
        $file = $form->get('file')->getData();
        $fileErrors = [];
        
        if (!$file) {
            $fileErrors[] = 'Le fichier est obligatoire';
        }
        
        // Si aucune erreur
        if (count($errors) === 0 && count($fileErrors) === 0 && $form->isValid()) {
            if ($file) {
                // Get file properties BEFORE moving it
                $fileSize = $file->getSize();
                $fileType = $file->getMimeType();
                
                // Générez un nom de fichier unique
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->sanitizeFilename($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                
                // Déplacez le fichier dans le dossier de stockage
                try {
                    $file->move(
                        $this->getParameter('depot_directory'),
                        $newFilename
                    );
                    
                    $depot->setFilePath($newFilename);
                    $depot->setFileSize((string) $fileSize);
                    $depot->setFileType($fileType);
                    
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du fichier: ' . $e->getMessage());
                    return $this->redirectToRoute('app_student_project_detail', ['id' => $id]);
                }
            }
            
            $entityManager->persist($depot);
            $entityManager->flush();
            
            $this->addFlash('success', 'Dépôt ajouté avec succès !');
            return $this->redirectToRoute('app_student_project_detail', ['id' => $id]);
        } else {
            // Afficher seulement les erreurs importantes
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            
            foreach ($fileErrors as $fileError) {
                $this->addFlash('error', $fileError);
            }
        }
    }
    
    return $this->render('student/depot/new.html.twig', [
        'project' => $project,
        'form' => $form->createView(),
    ]);
}

    #[Route('/depot/{id}/download', name: 'app_student_depot_download', methods: ['GET'])]
    public function downloadDepot(int $id, DepotRepository $depotRepository, EntityManagerInterface $entityManager): Response
    {
        $depot = $depotRepository->find($id);
        
        if (!$depot) {
            throw $this->createNotFoundException('Dépôt non trouvé');
        }
        
        // Incrémenter le compteur de téléchargements
        $depot->incrementDownloadCount();
        $entityManager->flush();
        
        // Chemin vers le fichier
        $filePath = $this->getParameter('depot_directory') . '/' . $depot->getFilePath();
        
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }
        
        return $this->file($filePath);
    }

#[Route('/depot/{id}/delete', name: 'app_student_depot_delete', methods: ['POST'])]
public function deleteDepot(Request $request, int $id, DepotRepository $depotRepository, EntityManagerInterface $entityManager): Response
{
    $depot = $depotRepository->find($id);
    
    if (!$depot) {
        throw $this->createNotFoundException('Dépôt non trouvé');
    }
    
    $projectId = $depot->getProject()->getId();
    
    // Optionnel: garder la vérification CSRF pour la sécurité
    $token = $request->request->get('_token');
    if (!$this->isCsrfTokenValid('delete' . $depot->getId(), $token)) {
        $this->addFlash('error', 'Token CSRF invalide.');
        return $this->redirectToRoute('app_student_project_detail', ['id' => $projectId]);
    }
    
    // Supprimer le fichier physique
    $filePath = $this->getParameter('depot_directory') . '/' . $depot->getFilePath();
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Supprimer de la base de données
    $entityManager->remove($depot);
    $entityManager->flush();
    
    $this->addFlash('success', 'Dépôt supprimé avec succès.');
    return $this->redirectToRoute('app_student_project_detail', ['id' => $projectId]);
}

    #[Route('/projects/{id}/depots', name: 'app_student_project_depots', methods: ['GET'])]
    public function projectDepots(int $id, ProjectRepository $projectRepository, DepotRepository $depotRepository): Response
    {
        $project = $projectRepository->find($id);
        
        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé');
        }
        
        // Récupérer les dépôts depuis la base de données
        $depots = $depotRepository->findByProject($id);
        
        // Transformer en tableau pour le template
        $depotsData = array_map(function($depot) {
            return [
                'id' => $depot->getId(),
                'title' => $depot->getTitle(),
                'description' => $depot->getDescription(),
                'type' => $depot->getType(),
                'file_size' => $depot->getFormattedFileSize(),
                'uploaded_at' => $depot->getUploadedAt()->format('d/m/Y'),
                'student_name' => $depot->getStudentName(),
                'download_count' => $depot->getDownloadCount(),
                'type_icon' => $depot->getTypeIcon(),
                'type_color' => $depot->getTypeColor()
            ];
        }, $depots);
        
        return $this->render('student/_depots_list.html.twig', [
            'project' => $project,
            'depots' => $depotsData,
        ]);
    }

#[Route('/depot/{id}/edit', name: 'app_student_depot_edit', methods: ['GET', 'POST'])]
public function editDepot(Request $request, int $id, DepotRepository $depotRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
{
    $depot = $depotRepository->find($id);
    
    if (!$depot) {
        throw $this->createNotFoundException('Dépôt non trouvé');
    }
    
    // Get the project from the depot
    $project = $depot->getProject();
    
    $form = $this->createForm(DepotType::class, $depot, [
        'is_edit' => true,
        'allow_file_change' => false
    ]);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted()) {
        // Validation SIMPLE - seulement pour type et description
        $errors = $validator->validate($depot, null, ['Default']);
        
        // Si aucune erreur
        if (count($errors) === 0 && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Dépôt modifié avec succès !');
            return $this->redirectToRoute('app_student_project_detail', ['id' => $project->getId()]);
        } else {
            // Afficher seulement les erreurs importantes
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }
    }
    
    return $this->render('student/depot/edit.html.twig', [
        'depot' => $depot,
        'project' => $project,
        'form' => $form->createView(),
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

    private function getDisplayStatus(string $status): string
    {
        $statusMap = [
            'draft' => 'Brouillon',
            'active' => 'Recherche membres',
            'completed' => 'Complet',
            'cancelled' => 'Annulé'
        ];
        
        return $statusMap[$status] ?? $status;
    }

    private function calculateDuration($project): string
    {
        $start = $project->getStartDate();
        $end = $project->getEndDate();
        
        if (!$end) {
            return 'En cours';
        }
        
        $diff = $start->diff($end);
        $months = $diff->m + ($diff->y * 12);
        
        if ($months > 0) {
            return $months . ' mois';
        }
        
        return $diff->days . ' jours';
    }


        private function sanitizeFilename(string $filename): string
    {
        // Supprimer les caractères dangereux
        $filename = preg_replace('/[^\p{L}\p{N}\s\-_\.]/u', '', $filename);
        // Limiter la longueur
        $filename = substr($filename, 0, 100);
        // Supprimer les points multiples
        $filename = preg_replace('/\.+/', '.', $filename);
        // Remplacer les espaces par des tirets
        $filename = str_replace(' ', '-', $filename);
        // Convertir en minuscules
        $filename = strtolower($filename);
        
        return $filename;
    }

}
