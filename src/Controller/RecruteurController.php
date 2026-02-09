<?php

namespace App\Controller;

use App\Entity\OffreStage;
use App\Entity\StageCondidature;
use App\Form\OffreStageType;
use App\Repository\OffreStageRepository;
use App\Repository\StageCondidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recruteur')]
class RecruteurController extends AbstractController
{
    #[Route('/acceuil', name: 'app_recruiter_home')]
    public function accueil(Request $request, StageCondidatureRepository $stageCondidatureRepository, OffreStageRepository $offreStageRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->handleStages($request, $stageCondidatureRepository, $offreStageRepository, $entityManager, 'recruteur/accueil.html.twig');
    }

    #[Route('/stages', name: 'app_recruteur_stages')]
    public function stages(Request $request, OffreStageRepository $offreStageRepository, StageCondidatureRepository $stageCondidatureRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->handleOffers($request, $offreStageRepository, $stageCondidatureRepository, $entityManager, 'recruteur/stages.html.twig');
    }

    private function handleStages(Request $request, StageCondidatureRepository $repository, OffreStageRepository $offreStageRepository, EntityManagerInterface $entityManager, string $template): Response
    {
        $search = $request->query->get('searchbar', '');
        $domaine = $request->query->get('domaine', 'all');
        $minDateStr = $request->query->get('min_date');
        $maxDateStr = $request->query->get('max_date');
        $sort = $request->query->get('sort', 'desc');
        $typeRequest = $request->query->get('type_request', 'all');
        $idOffre = $request->query->get('id_offre');

        $minDate = $minDateStr ? new \DateTime('@' . $minDateStr) : null;
        $maxDate = $maxDateStr ? new \DateTime('@' . $maxDateStr) : null;

        // Filter candidatures for the recruiter's offers
        $condidatures = $repository->searchAll(
            $search,
            $domaine,
            $minDate,
            $maxDate,
            $sort,
            $typeRequest,
            $idOffre ? (int) $idOffre : null,
            null,
            $this->getUser()
        );

        $recentCandidatures = $repository->findRecentByRecruiter($this->getUser());
        $domains = $repository->getDistinctDomains();
        $allOffers = $offreStageRepository->findAll();
        $dateRangeRaw = $repository->getDateRange();

        // Fallback for date range
        $minR = $dateRangeRaw['minDate'] ?? null;
        $maxR = $dateRangeRaw['maxDate'] ?? null;

        if (!$minR instanceof \DateTimeInterface) {
            $minR = $minR ? new \DateTime($minR) : new \DateTime('-1 month');
        }
        if (!$maxR instanceof \DateTimeInterface) {
            $maxR = $maxR ? new \DateTime($maxR) : new \DateTime('+1 day');
        }

        if ($minR->format('Y-m-d') === $maxR->format('Y-m-d')) {
            $minR = (clone $minR)->modify('-7 days');
            $maxR = (clone $maxR)->modify('+1 day');
        }

        $dateRange = [
            'min' => $minR->getTimestamp(),
            'max' => $maxR->getTimestamp(),
        ];

        // Handle Publication Form
        $offre = new OffreStage();
        $form = $this->createForm(OffreStageType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$offre->getDatePublication()) {
                $offre->setDatePublication(new \DateTime());
            }
            if (!$offre->getStatut()) {
                $offre->setStatut('ouverte');
            }

            // Automatically assign the current logged-in user as the recruiter
            $offre->setIdRecruteur($this->getUser());

            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de stage a été publiée avec succès !');
            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('recruteur/_job_cards.html.twig', [
                'condidatures' => $condidatures,
            ]);
        }

        return $this->render($template, [
            'condidatures' => $condidatures,
            'domains' => $domains,
            'dateRange' => $dateRange,
            'form' => $form->createView(),
            'current_filters' => [
                'search' => $search,
                'domaine' => $domaine,
                'min_date' => $request->query->get('min_date'),
                'max_date' => $request->query->get('max_date'),
                'sort' => $sort,
                'type_request' => $typeRequest,
                'id_offre' => $idOffre
            ],
            'all_offers' => $allOffers,
            'recent_candidatures' => $recentCandidatures
        ]);
    }

    private function handleOffers(Request $request, OffreStageRepository $offreStageRepository, StageCondidatureRepository $stageCondidatureRepository, EntityManagerInterface $entityManager, string $template): Response
    {
        $search = $request->query->get('searchbar', '');
        $entreprise = $request->query->get('entreprise', 'all');
        $sort = $request->query->get('sort', 'desc');
        $ownership = $request->query->get('ownership', 'all');
        $duree = $request->query->get('duree', 'all');
        $minDateStr = $request->query->get('min_date');
        $maxDateStr = $request->query->get('max_date');

        $minDate = $minDateStr ? (new \DateTime())->setTimestamp((int) $minDateStr) : null;
        $maxDate = $maxDateStr ? (new \DateTime())->setTimestamp((int) $maxDateStr) : null;

        $recentCandidatures = $stageCondidatureRepository->findRecentByRecruiter($this->getUser());

        // For the sake of this prototype, we'll assume the recruiter's company is "InnoLearn"
        $myEntreprise = "InnoLearn";

        $entrepriseFilter = null;
        $recruteurFilter = null;

        if ($ownership === 'mine') {
            $recruteurFilter = $this->getUser();
        } elseif ($entreprise !== 'all' && !empty($entreprise)) {
            $entrepriseFilter = $entreprise;
        }

        $offers = $offreStageRepository->searchAllStages(
            $search,
            $duree !== 'all' ? (int) $duree : null,
            $sort,
            $entrepriseFilter,
            $minDate,
            $maxDate,
            $recruteurFilter
        );

        $companies = $offreStageRepository->getDistinctCompanies();
        $durations = $offreStageRepository->getDistinctDurations();
        $dateRangeRaw = $offreStageRepository->getOfferDateRange();

        // Fallback for date range using same robust logic as StudentController
        $minR = isset($dateRangeRaw['minDate']) ? new \DateTime($dateRangeRaw['minDate']) : new \DateTime('-1 month');
        $maxR = isset($dateRangeRaw['maxDate']) ? new \DateTime($dateRangeRaw['maxDate']) : new \DateTime('+1 day');

        if ($minR->format('Y-m-d') === $maxR->format('Y-m-d')) {
            $minR = (clone $minR)->modify('-7 days');
            $maxR = (clone $maxR)->modify('+1 day');
        }

        $dateRange = [
            'min' => $minR->getTimestamp(),
            'max' => $maxR->getTimestamp(),
        ];

        // Handle Publication Form
        $offre = new OffreStage();
        $form = $this->createForm(OffreStageType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$offre->getDatePublication()) {
                $offre->setDatePublication(new \DateTime());
            }
            if (!$offre->getStatut()) {
                $offre->setStatut('ouverte');
            }

            // Automatically assign the current logged-in user as the recruiter
            $offre->setIdRecruteur($this->getUser());

            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre offre de stage a été publiée avec succès !');
            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('recruteur/_offer_cards.html.twig', [
                'offers' => $offers,
            ]);
        }

        return $this->render($template, [
            'offers' => $offers,
            'companies' => $companies,
            'durations' => $durations,
            'dateRange' => $dateRange,
            'form' => $form->createView(),
            'current_filters' => [
                'search' => $search,
                'entreprise' => $entreprise,
                'duree' => $duree,
                'sort' => $sort,
                'ownership' => $ownership,
                'min_date' => $request->query->get('min_date'),
                'max_date' => $request->query->get('max_date'),
            ],
            'recent_candidatures' => $recentCandidatures
        ]);
    }
    #[Route('/candidature/{id}/details', name: 'app_recruteur_candidature_details_ajax', methods: ['GET'])]
    public function showDetailsAjax(StageCondidature $condidature, OffreStageRepository $offreStageRepository): Response
    {
        // Fetch all offers by this recruiter for "Associer à une offre" feature
        $myOffers = $offreStageRepository->findBy(['id_recruteur' => $this->getUser()]);

        return $this->render('recruteur/_details_modal.html.twig', [
            'condidature' => $condidature,
            'my_offers' => $myOffers
        ]);
    }

    #[Route('/candidature/{id}/status/{status}', name: 'app_recruteur_candidature_status', methods: ['POST'])]
    public function updateStatus(
        StageCondidature $condidature,
        string $status,
        EntityManagerInterface $entityManager
    ): Response {
        if (!in_array($status, ['acceptée', 'refusée'])) {
            return $this->json(['success' => false, 'message' => 'Statut invalide.'], 400);
        }

        $condidature->setStatut($status);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'new_status' => $status
        ]);
    }

    #[Route('/candidature/{id}/associate/{offerId}', name: 'app_recruteur_candidature_associate', methods: ['POST'])]
    public function associateToOffer(
        StageCondidature $condidature,
        int $offerId,
        OffreStageRepository $offreStageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $offer = $offreStageRepository->find($offerId);
        if (!$offer || $offer->getIdRecruteur() !== $this->getUser()) {
            return $this->json(['success' => false, 'message' => 'Offre invalide.'], 400);
        }

        $condidature->setIdOffre($offer);
        $condidature->setTypeRequest('offre'); // Important: change type to offer application now
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Candidature associée à l\'offre avec succès.',
            'offre_titre' => $offer->getTitre()
        ]);
    }
}
