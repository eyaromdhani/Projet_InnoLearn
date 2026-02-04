<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/enseignant')]
class EnseignantController extends AbstractController
{
    #[Route('/dashboard', name: 'app_enseignant_dashboard')]
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

        return $this->render('enseignant/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
        ]);
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
            $entityManager->flush();

            return $this->redirectToRoute('app_enseignant_quizzes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('enseignant/quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }
}
