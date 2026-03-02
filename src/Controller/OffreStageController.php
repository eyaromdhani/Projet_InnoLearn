<?php

namespace App\Controller;

use App\Entity\OffreStage;
use App\Form\OffreStageType;
use App\Repository\OffreStageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre/stage')]
final class OffreStageController extends AbstractController
{



    #[Route(name: 'app_offre_stage_index', methods: ['GET'])]
    public function index(OffreStageRepository $offreStageRepository): Response
    {
        return $this->render('offre_stage/index.html.twig', [
            'offre_stages' => $offreStageRepository->findAll(),
        ]);
    }

    #[Route('/open', name: 'app_offre_stage_show_open', methods: ['GET'])]
    public function show_open(OffreStageRepository $offreStageRepository): Response
    {
        return $this->render('offre_stage/index.html.twig', [
            'offre_stages' => $offreStageRepository->findByStatut('ouverte'),
        ]);
    }



    #[Route('/new', name: 'app_offre_stage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offreStage = new OffreStage();
        $form = $this->createForm(OffreStageType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offreStage);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_stage/new.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_stage_show', methods: ['GET'])]
    public function show(OffreStage $offreStage): Response
    {
        return $this->render('offre_stage/show.html.twig', [
            'offre_stage' => $offreStage,
        ]);
    }





    #[Route('/search', name: 'app_offre_stage_search', methods: ['GET'])]
    public function search(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->search($input);


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }

    #[Route('/search_entreprise', name: 'app_offre_stage_search_entreprise', methods: ['GET'])]
    public function searchEntreprise(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->searchEntreprise($input);


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }

    #[Route('/search_date', name: 'app_offre_stage_search_date', methods: ['GET'])]
    public function searchDate(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->searchDate($input);


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }

    #[Route('/search_duree', name: 'app_offre_stage_search_duree', methods: ['GET'])]
    public function searchDuree(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->searchDuree((int) $input);


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }


    #[Route('/tri_desc', name: 'app_offre_stage_tri_desc', methods: ['GET'])]
    public function triDescendant(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->TriDescendant();


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }

    #[Route('/tri_asc', name: 'app_offre_stage_tri_asc', methods: ['GET'])]
    public function triAscendant(
        Request $request,
        OffreStageRepository $offreStageRepository
    ): Response {
        $input = $request->query->get('searchbar');

        $offres = [];

        if ($input) {
            $offres = $offreStageRepository->TriAscendant();


            return $this->render('offre_stage/index.html.twig', [
                'offre_stages' => $offres,
                'input' => $input,
            ]);
        } else {
            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);

        }
    }


    #[Route('/{id}/edit', name: 'app_offre_stage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreStageType::class, $offreStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre_stage/edit.html.twig', [
            'offre_stage' => $offreStage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_stage_delete', methods: ['POST'])]
    public function delete(Request $request, OffreStage $offreStage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offreStage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offreStage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_stage_index', [], Response::HTTP_SEE_OTHER);
    }
}
