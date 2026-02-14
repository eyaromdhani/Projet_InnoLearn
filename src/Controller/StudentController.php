<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;

#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/dashboard', name: 'app_student_dashboard')]
=======
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuizRepository;
use App\Repository\FormulaireRepository;
use App\Repository\ProjectRepository;
use App\Repository\DepotRepository;
use App\Repository\CategorieCoursRepository;
use App\Repository\CoursRepository;
use App\Entity\Cours;
use App\Form\CoursType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Depot;
use App\Form\DepotType;
use App\Repository\OffreStageRepository;
use App\Repository\StageCondidatureRepository;
use App\Entity\OffreStage;
use App\Entity\StageCondidature;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Dompdf\Dompdf;
use Dompdf\Options;

use App\Repository\EventRepository;
use App\Repository\InscritEventRepository;
use App\Enum\StatutEvenementEnum;
use App\Entity\InscritEvent;
use App\Form\InscritEventType;
use App\Entity\User;
#[Route('/student')]
class StudentController extends AbstractController
{
    #[Route('/home', name: 'app_student_home')]
>>>>>>> user
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
<<<<<<< HEAD
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

=======
    public function courses(CategorieCoursRepository $categorieCoursRepository): Response
    {
        $dbCategories = $categorieCoursRepository->findAll();
        $categories = [['name' => 'Tout', 'icon' => 'fa-th-large', 'active' => true]];
        foreach ($dbCategories as $cat) {
            $categories[] = [
                'name' => $cat->getTitre(),
                'icon' => 'fa-tag',
                'active' => false
            ];
        }

        return $this->render('student/courses.html.twig', [
            'categories' => $categories,
            'courses' => $dbCategories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_student_category_show', methods: ['GET', 'POST'])]
    public function categoryShow(int $id, CategorieCoursRepository $categorieCoursRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $categorieCoursRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $course = new Cours();
        $course->setCategorieCours($category);
        $course->setDateCreation(new \DateTime());

        // Try to set current user as teacher if logged in
        if ($user = $this->getUser()) {
            if ($user instanceof User) {
                $course->setEnseignant($user->getName() ?? $user->getUserIdentifier());
            }
        }

        $form = $this->createForm(CoursType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été ajouté avec succès !');
            return $this->redirectToRoute('app_student_category_show', ['id' => $id]);
        }

        return $this->render('student/category_show.html.twig', [
            'category' => $category,
            'courses' => $category->getCours(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/course/{id}', name: 'app_student_course_show')]
    public function courseShow(int $id, CoursRepository $coursRepository): Response
    {
        $course = $coursRepository->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        return $this->render('student/course_show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/course/edit/{id}', name: 'app_student_course_edit', methods: ['GET', 'POST'])]
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
            return $this->redirectToRoute('app_student_category_show', ['id' => $course->getCategorieCours()->getId()]);
        }

        return $this->render('student/category_show.html.twig', [
            'category' => $course->getCategorieCours(),
            'courses' => $course->getCategorieCours()->getCours(),
            'form' => $form->createView(),
            'edit_mode' => true,
            'edit_course_id' => $id
        ]);
    }

    #[Route('/course/delete/{id}', name: 'app_student_course_delete', methods: ['POST'])]
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

        return $this->redirectToRoute('app_student_category_show', ['id' => $categoryId]);
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
        // Set current user as student automatically
        $depot->setStudentName($this->getUser() ? $this->getUser()->getUserIdentifier() : 'Étudiant InnoLearn');

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
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

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
        $depotsData = array_map(function ($depot) {
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


>>>>>>> user
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
<<<<<<< HEAD
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
>>>>>>> user

        return $this->render('student/events.html.twig', [
            'categories' => $categories,
            'events' => $events,
<<<<<<< HEAD
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

=======
            'registrationStatuses' => $registrationStatuses,
            'eventCapacityStatus' => $eventCapacityStatus,
        ]);
    }

    #[Route('/event/participate', name: 'student_event_participate', methods: ['POST'])]
    public function participate(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, ValidatorInterface $validator): Response
    {
        $eventId = $request->request->get('event_id');
        $name = $request->request->get('name');
        $email = $request->request->get('email');

        if (!$eventId) {
            $this->addFlash('error', 'Événement non spécifié.');
            return $this->redirectToRoute('app_student_events');
        }

        $event = $eventRepository->find($eventId);
        if (!$event) {
            $this->addFlash('error', 'Événement non trouvé.');
            return $this->redirectToRoute('app_student_events');
        }

        $inscription = new InscritEvent();
        $inscription->setEvent($event);
        $inscription->setName($name ?? '');
        $inscription->setEmail($email ?? '');
        $inscription->setDateInscrit(new \DateTime());
        $inscription->setStatus('En attente');

        $errors = $validator->validate($inscription);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('app_student_events');
        }

        $entityManager->persist($inscription);
        $entityManager->flush();

        // Save email to session to remember the user
        $request->getSession()->set('student_email', $email);

        $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès.');
        return $this->redirectToRoute('app_student_events');
    }

    #[Route('/career', name: 'app_student_career')]
    public function career(
        Request $request,
        OffreStageRepository $offreStageRepository,
        StageCondidatureRepository $stageCondidatureRepository
    ): Response {
        // Get filter parameters
        $activeTab = $request->query->get('tab', 'offres');
        $search = $request->query->get('searchbar', '');
        $sort = $request->query->get('sort', 'desc');
        $entreprise = $request->query->get('entreprise', 'all');
        $domaine = $request->query->get('domaine', 'all');
        $duree = $request->query->get('duree', 'all');
        $ownership = $request->query->get('ownership', 'all');

        // Offer Date Range
        $offerDateBounds = $offreStageRepository->getOfferDateRange();
        $minOfferDate = isset($offerDateBounds['minDate']) ? new \DateTime($offerDateBounds['minDate']) : new \DateTime('-1 year');
        $maxOfferDate = isset($offerDateBounds['maxDate']) ? new \DateTime($offerDateBounds['maxDate']) : new \DateTime();

        if ($minOfferDate->format('Y-m-d') === $maxOfferDate->format('Y-m-d')) {
            $minOfferDate = (clone $minOfferDate)->modify('-7 days');
            $maxOfferDate = (clone $maxOfferDate)->modify('+1 day');
        }

        $minDateFilter = $request->query->get('min_date')
            ? (new \DateTime())->setTimestamp((int) $request->query->get('min_date'))
            : null;
        $maxDateFilter = $request->query->get('max_date')
            ? (new \DateTime())->setTimestamp((int) $request->query->get('max_date'))
            : null;

        // Fetch offers
        $offres = $offreStageRepository->searchAllStages(
            $search,
            $duree !== 'all' ? (int) $duree : null,
            $sort,
            $entreprise !== 'all' ? $entreprise : null,
            $minDateFilter,
            $maxDateFilter
        );

        $user = $this->getUser();
        // Fetch candidatures
        $candidatures = $stageCondidatureRepository->searchAll(
            $search,
            $domaine !== 'all' ? $domaine : null,
            $minDateFilter,
            $maxDateFilter,
            $sort,
            null,
            null,
            ($ownership === 'mine' && $user instanceof \App\Entity\User) ? $user : null
        );

        // Filter options
        $companies = $offreStageRepository->getDistinctCompanies();
        $durations = $offreStageRepository->getDistinctDurations();
        $domains = $stageCondidatureRepository->getDistinctDomains();

        // Candidature date range
        $candidatureBounds = $stageCondidatureRepository->getDateRange();
        $minCandidatureDate = isset($candidatureBounds['minDate']) ? new \DateTime($candidatureBounds['minDate']) : new \DateTime('-1 year');
        $maxCandidatureDate = isset($candidatureBounds['maxDate']) ? new \DateTime($candidatureBounds['maxDate']) : new \DateTime();

        if ($minCandidatureDate->format('Y-m-d') === $maxCandidatureDate->format('Y-m-d')) {
            $minCandidatureDate = (clone $minCandidatureDate)->modify('-7 days');
            $maxCandidatureDate = (clone $maxCandidatureDate)->modify('+1 day');
        }

        $currentFilters = [
            'tab' => $activeTab,
            'search' => $search,
            'sort' => $sort,
            'entreprise' => $entreprise,
            'domaine' => $domaine,
            'duree' => $duree,
            'ownership' => $ownership,
            'min_date' => $request->query->get('min_date'),
            'max_date' => $request->query->get('max_date'),
        ];

        if ($request->isXmlHttpRequest()) {
            if ($activeTab === 'candidatures') {
                return $this->render('student/_candidature_cards.html.twig', [
                    'candidatures' => $candidatures,
                ]);
            }
            return $this->render('student/_offer_cards_student.html.twig', [
                'offres' => $offres,
            ]);
        }

        return $this->render('student/career.html.twig', [
            'offres' => $offres,
            'candidatures' => $candidatures,
            'companies' => $companies,
            'durations' => $durations,
            'domains' => $domains,
            'current_filters' => $currentFilters,
            'offerDateRange' => [
                'min' => $minOfferDate->getTimestamp(),
                'max' => $maxOfferDate->getTimestamp(),
            ],
            'candidatureDateRange' => [
                'min' => $minCandidatureDate->getTimestamp(),
                'max' => $maxCandidatureDate->getTimestamp(),
            ],
        ]);
    }

    #[Route('/offer/{id}/details', name: 'app_student_offer_details_ajax', methods: ['GET'])]
    public function showOfferDetails(OffreStage $offre): Response
    {
        return $this->render('student/_offer_details_modal.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/offer/{id}/apply', name: 'app_student_offer_apply', methods: ['POST'])]
    public function applyToOffer(
        OffreStage $offre,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->json(['success' => false, 'message' => 'Vous devez être connecté pour postuler.'], 401);
        }

        $existing = $entityManager->getRepository(StageCondidature::class)->findOneBy([
            'id_etudiant' => $user,
            'id_offre' => $offre
        ]);

        if ($existing) {
            return $this->json(['success' => false, 'message' => 'Vous avez déjà postulé à cette offre.'], 400);
        }

        // Get FormData (not JSON anymore)
        $motivation = $request->request->get('motivation', '');
        $titre = trim($request->request->get('titre', ''));
        $domaine = trim($request->request->get('domaine', ''));
        $competences = trim($request->request->get('competences', ''));
        $description = trim($request->request->get('description', ''));
        $cvFile = $request->files->get('cv');

        if (strlen($titre) < 5) {
            return $this->json(['success' => false, 'message' => 'Le titre doit faire au moins 5 caractères.'], 400);
        }

        if (empty($domaine)) {
            return $this->json(['success' => false, 'message' => 'Le domaine est obligatoire.'], 400);
        }

        if (strlen($description) < 20) {
            return $this->json(['success' => false, 'message' => 'La description doit faire au moins 20 caractères.'], 400);
        }

        if (strlen($motivation) < 50) {
            return $this->json(['success' => false, 'message' => 'La lettre de motivation doit faire au moins 50 caractères.'], 400);
        }

        if (!$cvFile) {
            return $this->json(['success' => false, 'message' => 'Le CV est obligatoire.'], 400);
        }

        // Validate file type
        $allowedMimeTypes = ['application/pdf'];
        if (!in_array($cvFile->getMimeType(), $allowedMimeTypes)) {
            return $this->json(['success' => false, 'message' => 'Le CV doit être au format PDF.'], 400);
        }

        // Validate file size (5MB max)
        if ($cvFile->getSize() > 5 * 1024 * 1024) {
            return $this->json(['success' => false, 'message' => 'Le CV est trop volumineux (max 5 Mo).'], 400);
        }

        // Upload CV file
        $cvDirectory = $this->getParameter('cv_directory');
        $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

        try {
            $cvFile->move($cvDirectory, $newFilename);
        } catch (FileException $e) {
            return $this->json(['success' => false, 'message' => 'Erreur lors de l\'upload du CV.'], 500);
        }

        $candidature = new StageCondidature();
        $candidature->setIdOffre($offre);
        $candidature->setIdEtudiant($user);
        $candidature->setTitre($titre);
        $candidature->setDescription($description);
        $candidature->setDomaine($domaine);
        $candidature->setCompetences($competences);
        $candidature->setDatePublication(new \DateTime());
        $candidature->setStatut('en_attente');
        $candidature->setTypeRequest('offre');
        $candidature->setCv($newFilename);
        $candidature->setLettreMotivation($motivation);

        $entityManager->persist($candidature);
        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Votre candidature a été envoyée avec succès !']);
    }

    #[Route('/candidature/{id}', name: 'app_student_candidature_details_ajax', methods: ['GET'])]
    public function showCandidatureDetails(StageCondidature $candidature): Response
    {
        return $this->render('student/_candidature_details_modal.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/career/create-candidature', name: 'app_student_create_candidature', methods: ['POST'])]
    public function createCandidature(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['success' => false, 'message' => 'Requête invalide.'], 400);
        }

        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->json(['success' => false, 'message' => 'Vous devez être connecté.'], 401);
        }

        // Get form data (FormData, not JSON)
        $titre = trim($request->request->get('titre', ''));
        $domaine = trim($request->request->get('domaine', ''));
        $competences = trim($request->request->get('competences', ''));
        $description = trim($request->request->get('description', ''));
        $lettreMotivation = trim($request->request->get('lettre_motivation', ''));

        // Get uploaded CV file
        $cvFile = $request->files->get('cv');

        $errors = [];

        if (strlen($titre) < 5) {
            $errors[] = 'Le titre doit faire au moins 5 caractères.';
        }

        if (empty($domaine)) {
            $errors[] = 'Le domaine est obligatoire.';
        }

        if (!$cvFile) {
            $errors[] = 'Le CV est obligatoire.';
        } else {
            // Validate file type
            $allowedMimeTypes = ['application/pdf'];
            if (!in_array($cvFile->getMimeType(), $allowedMimeTypes)) {
                $errors[] = 'Le CV doit être au format PDF.';
            }

            // Validate file size (5MB max)
            if ($cvFile->getSize() > 5 * 1024 * 1024) {
                $errors[] = 'Le CV est trop volumineux (max 5 Mo).';
            }
        }

        if (empty($competences)) {
            $errors[] = 'Les compétences sont obligatoires.';
        }

        if (strlen($description) < 20) {
            $errors[] = 'La description doit faire au moins 20 caractères.';
        }

        if (strlen($lettreMotivation) < 50) {
            $errors[] = 'La lettre de motivation doit faire au moins 50 caractères.';
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'message' => implode(' ', $errors)], 400);
        }

        // Upload CV file
        $cvDirectory = $this->getParameter('cv_directory');
        $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

        try {
            $cvFile->move($cvDirectory, $newFilename);
        } catch (FileException $e) {
            return $this->json(['success' => false, 'message' => 'Erreur lors de l\'upload du CV.'], 500);
        }

        // Create new candidature
        $candidature = new StageCondidature();
        $candidature->setTitre($titre);
        $candidature->setDomaine($domaine);
        $candidature->setCv($newFilename);
        $candidature->setCompetences($competences);
        $candidature->setDescription($description);
        $candidature->setLettreMotivation($lettreMotivation);

        // Auto-populated fields
        $candidature->setTypeRequest('demande');
        $candidature->setDatePublication(new \DateTime());
        $candidature->setStatut('en_attente');
        $candidature->setIdEtudiant($user);
        $candidature->setIdOffre(null); // null for spontaneous applications

        $entityManager->persist($candidature);
        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Votre demande a été créée avec succès !']);
    }

>>>>>>> user
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
<<<<<<< HEAD
    public function quizzes(QuizRepository $quizRepository): Response
    {
        return $this->render('student/quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }
=======
    public function quizzes(\App\Repository\FormulaireRepository $formulaireRepository): Response
    {
        return $this->render('student/quiz/index.html.twig', [
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
                            if ((float) $uAns === (float) $cAns) {
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

>>>>>>> user
}
