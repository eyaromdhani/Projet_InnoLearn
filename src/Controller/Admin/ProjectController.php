<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/projects')]
// #[IsGranted('ROLE_ADMIN')]  // Temporarily commented for testing
class ProjectController extends AbstractController
{
    #[Route('/', name: 'admin_projects', methods: ['GET'])]
public function index(ProjectRepository $projectRepository, Request $request): Response
{
    $status = $request->query->get('status');
    $search = $request->query->get('search');
    
    $projects = $projectRepository->findByFilters($search, $status);
    
    // Récupérer TOUS les projets pour les stats
    $allProjects = $projectRepository->findAll();
    
    // Calculer les stats manuellement
    $stats = [
        'total' => count($allProjects),
        'draft' => 0,
        'active' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    foreach ($allProjects as $project) {
        switch ($project->getStatus()) {
            case 'draft': $stats['draft']++; break;
            case 'active': $stats['active']++; break;
            case 'completed': $stats['completed']++; break;
            case 'cancelled': $stats['cancelled']++; break;
        }
    }
    
    return $this->render('admin/project/index.html.twig', [
        'projects' => $projects,
        'current_status' => $status,
        'search_query' => $search,
        'stats' => $stats,
    ]);
}

    #[Route('/new', name: 'admin_projects_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedAt(new \DateTime());
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé avec succès.');

            return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'form_title' => 'Créer un nouveau projet'
        ]);
    }

    #[Route('/{id}', name: 'admin_projects_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('admin/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_projects_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Projet modifié avec succès.');

            return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/form.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'form_title' => 'Modifier le projet'
        ]);
    }

    #[Route('/{id}', name: 'admin_projects_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
            $this->addFlash('success', 'Projet supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_projects', [], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * Méthode auxiliaire pour calculer les statistiques
     * Utilisez cette méthode si les count() ne fonctionnent pas
     */
    private function calculateStats(ProjectRepository $projectRepository): array
    {
        $allProjects = $projectRepository->findAll();
        
        $stats = [
            'total' => count($allProjects),
            'draft' => 0,
            'active' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];
        
        foreach ($allProjects as $project) {
            switch ($project->getStatus()) {
                case 'draft': $stats['draft']++; break;
                case 'active': $stats['active']++; break;
                case 'completed': $stats['completed']++; break;
                case 'cancelled': $stats['cancelled']++; break;
            }
        }
        
        return $stats;
    }
}