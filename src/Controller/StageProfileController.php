<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\DepotRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/etudiant/stage-profil')]
#[IsGranted('ROLE_USER')]
final class StageProfileController extends AbstractController
{
    #[Route('/', name: 'app_stage_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ProfileRepository $profileRepository,
        DepotRepository $depotRepository,
        \App\Repository\ExperienceRepository $experienceRepository,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Load existing profile or create a new one
        $profile = $profileRepository->findByUser($user);
        $isNew = false;
        if (!$profile) {
            $profile = new Profile();
            $profile->setUser($user);
            $isNew = true;
        }

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $cvFile */
            $cvFile = $form->get('cvFile')->getData();

            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = 'cv_' . $user->getId() . '_' . uniqid() . '.pdf';

                try {
                    $cvFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/cv',
                        $newFilename
                    );

                    // Delete old CV if exists
                    if ($profile->getCv()) {
                        $oldPath = $this->getParameter('kernel.project_dir') . '/public/uploads/cv/' . $profile->getCv();
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $profile->setCv($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du CV.');
                }
            }

            if ($isNew) {
                $em->persist($profile);
            }
            $em->flush();

            $this->addFlash('success', 'Votre profil de stage a été enregistré avec succès !');
            return $this->redirectToRoute('app_student_career', ['tab' => 'profil']);
        }

        // Handle Add Experience Form
        $newExp = new \App\Entity\Experience();
        $newExp->setUser($user);
        $expForm = $this->createForm(\App\Form\ExperienceType::class, $newExp);
        $expForm->handleRequest($request);

        if ($expForm->isSubmitted() && $expForm->isValid()) {
            $em->persist($newExp);
            $em->flush();
            $this->addFlash('success', 'Nouvelle ligne ajoutée au parcours !');
            return $this->redirectToRoute('app_student_career', ['tab' => 'profil']);
        }

        // Fetch depots linked to this user (projects submitted by the student)
        $depots = $depotRepository->findBy(['user' => $user]);
        $experiences = $experienceRepository->findByUser($user);

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'expForm' => $expForm->createView(),
            'profile' => $profile,
            'depots' => $depots,
            'experiences' => $experiences,
        ]);
    }

    #[Route('/experience/delete/{id}', name: 'app_stage_profile_experience_delete', methods: ['POST'])]
    public function deleteExperience(
        \App\Entity\Experience $experience,
        EntityManagerInterface $em
    ): Response {
        if ($experience->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($experience);
        $em->flush();
        $this->addFlash('info', 'Élément supprimé.');

        return $this->redirectToRoute('app_student_career', ['tab' => 'profil']);
    }

    #[Route('/cv/generer', name: 'app_stage_profile_cv_generate', methods: ['GET'])]
    public function generateCv(
        ProfileRepository $profileRepository,
        DepotRepository $depotRepository,
        \App\Repository\ExperienceRepository $experienceRepository,
        DompdfFactoryInterface $dompdfFactory
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $profile = $profileRepository->findByUser($user);

        if (!$profile) {
            $this->addFlash('warning', 'Veuillez d\'abord compléter votre profil avant de générer un CV.');
            return $this->redirectToRoute('app_student_career', ['tab' => 'profil']);
        }

        $depots = $depotRepository->findBy(['user' => $user]);
        $experiences = $experienceRepository->findByUser($user);

        // --- Generate AI intro via OpenAI (Guzzle API) ---
        $aiIntro = $this->generateAiIntro($profile, $depots);

        // --- Render HTML for PDF ---
        $html = $this->renderView('profile/_cv_template.html.twig', [
            'user' => $user,
            'profile' => $profile,
            'depots' => $depots,
            'experiences' => $experiences,
            'aiIntro' => $aiIntro,
        ]);

        // --- Generate PDF with nucleos/dompdf-bundle ---
        $dompdf = $dompdfFactory->create();

        // Enable remote images (for avatars)
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();
        $filename = 'cv_' . preg_replace('/[^a-z0-9_]/i', '_', $user->getName() ?? 'etudiant') . '.pdf';

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function generateAiIntro(Profile $profile, array $depots): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

        if (!$apiKey || $apiKey === 'sk-xxxxxxx') {
            return sprintf(
                'Étudiant(e) en %s de niveau %s, je maîtrise : %s. '
                . 'Motivé(e) et rigoureux(se), je suis à la recherche d\'un stage enrichissant pour mettre en pratique mes compétences.',
                $profile->getDomaine(),
                $profile->getNiveauAcademique(),
                mb_strimwidth($profile->getCompetences(), 0, 120, '...')
            );
        }

        $projectTitles = array_map(fn($d) => $d->getTitle(), $depots);
        $projectsList = empty($projectTitles) ? 'Aucun projet listé.' : implode(', ', $projectTitles);

        $prompt = "Tu es un expert en rédaction de CV. Génère une accroche professionnelle courte (3 phrases maximum) pour un(e) étudiant(e) :\n"
            . "- Domaine : {$profile->getDomaine()}\n"
            . "- Niveau académique : {$profile->getNiveauAcademique()}\n"
            . "- Compétences : {$profile->getCompetences()}\n"
            . "- Projets réalisés : $projectsList\n"
            . "Rédige une accroche concise, impactante et professionnelle en français.";

        try {
            $client = new Client(['timeout' => 10]);
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant expert en rédaction de CV professionnels.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.6,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return trim($data['choices'][0]['message']['content'] ?? '');
        } catch (\Exception $e) {
            return sprintf(
                'Passionné(e) par le domaine de %s (niveau %s), je mets mes compétences au service de projets innovants.',
                $profile->getDomaine(),
                $profile->getNiveauAcademique()
            );
        }
    }
}
