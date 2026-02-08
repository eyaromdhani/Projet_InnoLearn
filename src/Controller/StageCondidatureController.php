<?php

namespace App\Controller;

use App\Entity\StageCondidature;
use App\Form\StageCondidatureType;
use App\Repository\StageCondidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stage/condidature')]
final class StageCondidatureController extends AbstractController
{
    #[Route(name: 'app_stage_condidature_index', methods: ['GET'])]
    public function index(StageCondidatureRepository $stageCondidatureRepository): Response
    {
        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $stageCondidatureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_stage_condidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stageCondidature = new StageCondidature();
        $form = $this->createForm(StageCondidatureType::class, $stageCondidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stageCondidature);
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_condidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage_condidature/new.html.twig', [
            'stage_condidature' => $stageCondidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stage_condidature_show', methods: ['GET'])]
    public function show(StageCondidature $stageCondidature): Response
    {
        return $this->render('stage_condidature/show.html.twig', [
            'stage_condidature' => $stageCondidature,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stage_condidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StageCondidature $stageCondidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StageCondidatureType::class, $stageCondidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_stage_condidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stage_condidature/edit.html.twig', [
            'stage_condidature' => $stageCondidature,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_stage_condidature_search', methods: ['GET'])]
    public function search(Request $request, StageCondidatureRepository $repository): Response
    {
        $input = $request->query->get('searchbar');
        $condidatures = $input ? $repository->searchAll($input) : $repository->findAll();

        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $condidatures,
            'input' => $input,
        ]);
    }

    #[Route('/search/statut', name: 'app_stage_condidature_search_statut', methods: ['GET'])]
    public function searchStatut(Request $request, StageCondidatureRepository $repository): Response
    {
        $statut = $request->query->get('statut');
        $condidatures = $statut ? $repository->searchByStatut($statut) : $repository->findAll();

        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $condidatures,
        ]);
    }

    #[Route('/search/offre', name: 'app_stage_condidature_search_offre', methods: ['GET'])]
    public function searchOffre(Request $request, StageCondidatureRepository $repository): Response
    {
        $idOffre = $request->query->get('id_offre');
        $condidatures = $idOffre ? $repository->searchByOffre((int) $idOffre) : $repository->findAll();

        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $condidatures,
        ]);
    }

    #[Route('/filter/date', name: 'app_stage_condidature_filter_date', methods: ['GET'])]
    public function filterDate(Request $request, StageCondidatureRepository $repository): Response
    {
        $minDateStr = $request->query->get('min_date');
        $maxDateStr = $request->query->get('max_date');

        if ($minDateStr && $maxDateStr) {
            $minDate = new \DateTime($minDateStr);
            $maxDate = new \DateTime($maxDateStr);
            $condidatures = $repository->filterByDatePublication($minDate, $maxDate);
        } else {
            $condidatures = $repository->findAll();
        }

        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $condidatures,
        ]);
    }

    #[Route('/tri/asc', name: 'app_stage_condidature_tri_asc', methods: ['GET'])]
    public function triAsc(StageCondidatureRepository $repository): Response
    {
        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $repository->sortByDateAsc(),
        ]);
    }

    #[Route('/tri/desc', name: 'app_stage_condidature_tri_desc', methods: ['GET'])]
    public function triDesc(StageCondidatureRepository $repository): Response
    {
        return $this->render('stage_condidature/index.html.twig', [
            'stage_condidatures' => $repository->sortByDateDesc(),
        ]);
    }

    #[Route('/{id}', name: 'app_stage_condidature_delete', methods: ['POST'])]
    public function delete(Request $request, StageCondidature $stageCondidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stageCondidature->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stageCondidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_stage_condidature_index', [], Response::HTTP_SEE_OTHER);
    }
}
