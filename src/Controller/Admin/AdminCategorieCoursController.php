<?php

namespace App\Controller\Admin;

use App\Entity\CategorieCours;
use App\Form\CategorieCoursType;
use App\Repository\CategorieCoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cours;
use App\Repository\CoursRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;

#[Route('/admin/categorie-cours')]
final class AdminCategorieCoursController extends AbstractController
{
    #[Route(name: 'admin_categorie_cours_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CategorieCoursRepository $categorieCoursRepository, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('q');

        $categorieCour = new CategorieCours();
        $form = $this->createForm(CategorieCoursType::class, $categorieCour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieCour);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie de cours ajoutée avec succès !');

            return $this->redirectToRoute('admin_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie_cours/index.html.twig', [
            'categorie_cours' => $categorieCoursRepository->findBySearch($query),
            'form' => $form->createView(),
            'query' => $query
        ]);
    }

    #[Route('/new', name: 'admin_categorie_cours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieCour = new CategorieCours();
        $form = $this->createForm(CategorieCoursType::class, $categorieCour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieCour);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie_cours/new.html.twig', [
            'categorie_cour' => $categorieCour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_cours_show', methods: ['GET'])]
    public function show(CategorieCours $categorieCour): Response
    {
        return $this->render('admin/categorie_cours/show.html.twig', [
            'categorie_cour' => $categorieCour,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_cours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieCours $categorieCour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieCoursType::class, $categorieCour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie_cours/edit.html.twig', [
            'categorie_cour' => $categorieCour,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_cours_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieCours $categorieCour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorieCour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieCour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_categorie_cours_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/course/{id}/pdf', name: 'admin_course_pdf', methods: ['GET'])]
    public function downloadCoursePdf(int $id, CoursRepository $coursRepository): Response
    {
        $course = $coursRepository->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        $content = sprintf(
            "RAPPORT DE COURS INNOLEARN\n\n" .
            "ID: #%d\n" .
            "Nom: %s\n" .
            "Catégorie: %s\n" .
            "Enseignant: %s\n" .
            "Durée: %d heures\n" .
            "Niveau: %s\n" .
            "Description: %s\n\n" .
            "Généré le: %s",
            $course->getId(),
            (string) $course->getNom(),
            $course->getCategorieCours()?->getTitre() ?? 'N/A',
            (string) $course->getEnseignant(),
            $course->getDuree(),
            $course->getNiveau(),
            $course->getDescription(),
            (new \DateTime())->format('d/m/Y H:i')
        );

        $response = new Response($content);
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            sprintf('cours-%s.pdf', $course->getSlug())
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
