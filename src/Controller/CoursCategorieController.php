<?php

namespace App\Controller;

use App\Entity\CoursCategorie;
use App\Form\CoursCategorieType;
use App\Repository\CoursCategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cours/categorie')]
final class CoursCategorieController extends AbstractController
{
    #[Route(name: 'app_cours_categorie_index', methods: ['GET'])]
    public function index(CoursCategorieRepository $coursCategorieRepository): Response
    {
        return $this->render('cours_categorie/index.html.twig', [
            'cours_categories' => $coursCategorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cours_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $coursCategorie = new CoursCategorie();
        $form = $this->createForm(CoursCategorieType::class, $coursCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($coursCategorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours_categorie/new.html.twig', [
            'cours_categorie' => $coursCategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_categorie_show', methods: ['GET'])]
    public function show(CoursCategorie $coursCategorie): Response
    {
        return $this->render('cours_categorie/show.html.twig', [
            'cours_categorie' => $coursCategorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cours_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CoursCategorie $coursCategorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoursCategorieType::class, $coursCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cours_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cours_categorie/edit.html.twig', [
            'cours_categorie' => $coursCategorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cours_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, CoursCategorie $coursCategorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coursCategorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($coursCategorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cours_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
