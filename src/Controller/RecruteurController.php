<?php

namespace App\Controller;

use App\Entity\OffreStage;
use App\Entity\User;
use App\Entity\StageCondidature;
use App\Form\OffreStageType;
use App\Repository\OffreStageRepository;
use App\Repository\StageCondidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/recruteur')]
class RecruteurController extends AbstractController
{
    private $aiService;

    public function __construct(\App\Service\AIService $aiService)
    {
        $this->aiService = $aiService;
    }

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

        $user = $this->getUser();
        if (!$user instanceof User) {
            $condidatures = [];
            $recentCandidatures = [];
        } else {
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
                $user
            );

            $recentCandidatures = $repository->findRecentByRecruiter($user);
        }
        $domains = $repository->getDistinctDomains();
        $allOffers = $offreStageRepository->findAll();
        $dateRangeRaw = $repository->getDateRange();

        // Fallback for date range
        $minR = $dateRangeRaw['minDate'] ?? null;
        $maxR = $dateRangeRaw['maxDate'] ?? null;

        if (!$minR instanceof \DateTime) {
            $minR = $minR ? new \DateTime($minR) : new \DateTime('-1 month');
        }
        if (!$maxR instanceof \DateTime) {
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
            $user = $this->getUser();
            if ($user instanceof User) {
                $offre->setIdRecruteur($user);
            }

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
            'recent_candidatures' => $recentCandidatures,
            'recruiterStats' => [
                'myOffersCount' => $user instanceof User ? $offreStageRepository->count(['id_recruteur' => $user]) : 0,
                'totalApplications' => $user instanceof User ? $repository->countByRecruiter($user) : 0,
                'accepted' => $user instanceof User ? $repository->countByRecruiter($user, 'acceptée') : 0,
                'pending' => $user instanceof User ? $repository->countByRecruiter($user, 'en attente') : 0,
            ]
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

        $user = $this->getUser();
        $recentCandidatures = $user instanceof User ? $stageCondidatureRepository->findRecentByRecruiter($user) : [];

        // For the sake of this prototype, we'll assume the recruiter's company is "InnoLearn"
        $myEntreprise = "InnoLearn";

        $entrepriseFilter = null;
        $recruteurFilter = null;

        $user = $this->getUser();
        if ($ownership === 'mine' && $user instanceof User) {
            $recruteurFilter = $user;
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
            $user = $this->getUser();
            if ($user instanceof User) {
                $offre->setIdRecruteur($user);
            }

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
            'recent_candidatures' => $recentCandidatures,
            'recruiterStats' => [
                'myOffersCount' => $user instanceof User ? $offreStageRepository->count(['id_recruteur' => $user]) : 0,
                'totalApplications' => $user instanceof User ? $stageCondidatureRepository->countByRecruiter($user) : 0,
                'accepted' => $user instanceof User ? $stageCondidatureRepository->countByRecruiter($user, 'acceptée') : 0,
                'pending' => $user instanceof User ? $stageCondidatureRepository->countByRecruiter($user, 'en attente') : 0,
            ]
        ]);
    }
    #[Route('/candidature/{id}/details', name: 'app_recruteur_candidature_details_ajax', methods: ['GET'])]
    public function showDetailsAjax(StageCondidature $condidature, OffreStageRepository $offreStageRepository): Response
    {
        // Fetch all offers by this recruiter for "Associer à une offre" feature
        $user = $this->getUser();
        $myOffers = $user instanceof User ? $offreStageRepository->findBy(['id_recruteur' => $user]) : [];

        return $this->render('recruteur/_details_modal.html.twig', [
            'condidature' => $condidature,
            'my_offers' => $myOffers
        ]);
    }

    #[Route('/candidature/{id}/status/{status}', name: 'app_recruteur_candidature_status', methods: ['POST'])]
    public function updateStatus(
        StageCondidature $condidature,
        string $status,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        if (!in_array($status, ['acceptée', 'refusée'])) {
            return $this->json(['success' => false, 'message' => 'Statut invalide.'], 400);
        }

        $condidature->setStatut($status);
        $entityManager->flush();

        if ($status === 'acceptée') {
            $student = $condidature->getIdEtudiant();
            if ($student && $student->getEmail()) {
                $email = (new Email())
                    ->from('hr@innolearn.com')
                    ->to($student->getEmail())
                    ->subject('Félicitations ! Votre candidature a été acceptée')
                    ->html(sprintf(
                        "<h1>Félicitations %s !</h1><p>Nous avons le plaisir de vous informer que votre candidature pour le stage <strong>'%s'</strong> a été acceptée.</p><p>Le recruteur vous contactera prochainement pour les détails.</p><p>L'équipe InnoLearn</p>",
                        $student->getName(),
                        $condidature->getTitre()
                    ));

                try {
                    $mailer->send($email);
                } catch (\Exception $e) {
                    // Log error but don't fail the status update
                    // In a production app, we would use a logger here
                }
            }
        }

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
        $user = $this->getUser();
        if (!$offer || !$user instanceof User || $offer->getIdRecruteur() !== $user) {
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

    #[Route('/candidature/{id}/ai-match', name: 'app_recruteur_ai_matching_ajax', methods: ['GET'])]
    public function aiMatchingAjax(StageCondidature $condidature): Response
    {
        $offer = $condidature->getIdOffre();
        if (!$offer) {
            return $this->json(['success' => false, 'message' => 'Candidature non associée à une offre.'], 400);
        }

        $offerData = [
            'titre' => $offer->getTitre(),
            'description' => $offer->getDescription(),
            'competences' => $offer->getCompetences()
        ];

        $candidateData = [
            'nom' => $condidature->getIdEtudiant() ? $condidature->getIdEtudiant()->getName() : 'Candidat Anonyme',
            'domaine' => $condidature->getDomaine(),
            'competences' => $condidature->getCompetences(),
            'experiences' => $condidature->getDescription() // Contextual info
        ];

        $result = $this->aiService->determineMatchingScore($offerData, $candidateData);

        return $this->json($result);
    }
}
