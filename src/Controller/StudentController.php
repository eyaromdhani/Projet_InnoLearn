<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\ProjectRepository;

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
                'category' => 'Développement', // fixe
                'lead' => 'Équipe InnoLearn', // fixe
                'members' => 1, // fixe
                'max_members' => 3, // fixe
                'technologies' => ['Symfony', 'PHP', 'Twig'], // fixe
                'difficulty' => 'Intermédiaire', // fixe
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
    public function quizzes(QuizRepository $quizRepository): Response
    {
        return $this->render('student/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }
}
