<?php

namespace App\Controller\Admin;

use App\Entity\OffreStage;
use App\Entity\StageCondidature;
use App\Form\OffreStageType;
use App\Form\StageCondidatureType;
use App\Repository\OffreStageRepository;
use App\Repository\StageCondidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/opportunity')]
class OpportunityController extends AbstractController
{
    // --- OFFRES DE STAGE ---

    #[Route('/offres', name: 'admin_opportunity_offres_index', methods: ['GET'])]
    public function offresIndex(OffreStageRepository $repository): Response
    {
        return $this->render('admin/opportunity/offres/index.html.twig', [
            'offres' => $repository->findAll(),
            'current_tab' => 'offres'
        ]);
    }

    #[Route('/offres/new', name: 'admin_opportunity_offres_new', methods: ['GET', 'POST'])]
    public function offresNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new OffreStage();
        $form = $this->createForm(OffreStageType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();
            return $this->redirectToRoute('admin_opportunity_offres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/opportunity/offres/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'current_tab' => 'offres'
        ]);
    }

    #[Route('/offres/{id}/edit', name: 'admin_opportunity_offres_edit', methods: ['GET', 'POST'])]
    public function offresEdit(Request $request, OffreStage $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreStageType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_opportunity_offres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/opportunity/offres/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'current_tab' => 'offres'
        ]);
    }

    #[Route('/offres/{id}', name: 'admin_opportunity_offres_delete', methods: ['POST'])]
    public function offresDelete(Request $request, OffreStage $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_opportunity_offres_index', [], Response::HTTP_SEE_OTHER);
    }

    // --- CANDIDATURES ---

    #[Route('/applications', name: 'admin_opportunity_applications_index', methods: ['GET'])]
    public function applicationsIndex(StageCondidatureRepository $repository): Response
    {
        return $this->render('admin/opportunity/applications/index.html.twig', [
            'applications' => $repository->findAll(),
            'current_tab' => 'applications'
        ]);
    }

    #[Route('/applications/new', name: 'admin_opportunity_applications_new', methods: ['GET', 'POST'])]
    public function applicationsNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $application = new StageCondidature();
        $form = $this->createForm(StageCondidatureType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cv')->getData();

            if ($cvFile) {
                $cvDirectory = $this->getParameter('cv_directory');
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

                try {
                    $cvFile->move($cvDirectory, $newFilename);
                    $application->setCv($newFilename);
                } catch (FileException $e) {
                    // Handle exception if needed
                }
            }

            if (!$application->getDatePublication()) {
                $application->setDatePublication(new \DateTime());
            }

            if (!$application->getStatut()) {
                $application->setStatut('en_attente');
            }

            $entityManager->persist($application);
            $entityManager->flush();
            return $this->redirectToRoute('admin_opportunity_applications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/opportunity/applications/new.html.twig', [
            'application' => $application,
            'form' => $form,
            'current_tab' => 'applications'
        ]);
    }

    #[Route('/applications/{id}/edit', name: 'admin_opportunity_applications_edit', methods: ['GET', 'POST'])]
    public function applicationsEdit(Request $request, StageCondidature $application, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StageCondidatureType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cv')->getData();

            if ($cvFile) {
                $cvDirectory = $this->getParameter('cv_directory');
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

                try {
                    $cvFile->move($cvDirectory, $newFilename);
                    $application->setCv($newFilename);
                } catch (FileException $e) {
                    // Handle exception if needed
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('admin_opportunity_applications_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/opportunity/applications/edit.html.twig', [
            'application' => $application,
            'form' => $form,
            'current_tab' => 'applications'
        ]);
    }

    #[Route('/applications/{id}', name: 'admin_opportunity_applications_delete', methods: ['POST'])]
    public function applicationsDelete(Request $request, StageCondidature $application, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $application->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($application);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_opportunity_applications_index', [], Response::HTTP_SEE_OTHER);
    }
}
