<?php

namespace App\Controller;

use App\Entity\GameAvatar;
use App\Entity\User;
use App\Repository\GameAvatarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/avatar')]
#[IsGranted('ROLE_USER')]
class AvatarController extends AbstractController
{
    private string $pythonServiceUrl = 'http://localhost:8001';

    #[Route('/outfits', name: 'api_avatar_outfits', methods: ['GET'])]
    public function listOutfits(#[Autowire('%kernel.project_dir%')] string $projectDir): JsonResponse
    {
        $outfitsDir = $projectDir . '/public/3d/avatar/outfits';
        $outfits = [];
        
        if (is_dir($outfitsDir)) {
            $files = scandir($outfitsDir);
            foreach ($files as $file) {
                if (str_ends_with($file, '.glb')) {
                    $stem = preg_replace('/\.glb$/i', '', $file) ?? $file;
                    $name = preg_replace('/^outfit_/i', '', $stem) ?? $stem;
                    $label = ucwords(str_replace(['_', '-'], ' ', $name));
                    $outfits[] = [
                        'id' => $name,
                        'name' => $label,
                        'path' => 'outfits/' . $file
                    ];
                }
            }
        }

        usort($outfits, static fn(array $a, array $b) => strcmp((string) $a['name'], (string) $b['name']));
        
        // Fallback or default outfits if directory is empty or doesn't exist
        if (empty($outfits)) {
            $outfits = [
                ['id' => 'outfit', 'name' => 'Default Outfit', 'path' => 'outfits/outfit.glb'],
                ['id' => 'male_formal_outfit', 'name' => 'Male Formal Outfit', 'path' => 'outfits/male_formal_outfit.glb'],
                ['id' => 'librarian_outfit', 'name' => 'Librarian Outfit', 'path' => 'outfits/librarian_outfit.glb'],
            ];
        }

        return new JsonResponse($outfits);
    }

    #[Route('/hairs', name: 'api_avatar_hairs', methods: ['GET'])]
    public function listHairs(#[Autowire('%kernel.project_dir%')] string $projectDir): JsonResponse
    {
        $hairDir = $projectDir . '/public/3d/avatar/hair';
        $hairs = [];

        if (is_dir($hairDir)) {
            $files = scandir($hairDir);
            foreach ($files as $file) {
                if (str_ends_with(strtolower($file), '.glb')) {
                    $stem = preg_replace('/\.glb$/i', '', $file) ?? $file;
                    $id = strtolower(str_replace(' ', '_', $stem));
                    $hairs[] = [
                        'id' => $id,
                        'name' => ucwords(str_replace(['_', '-'], ' ', $stem)),
                        'path' => 'hair/' . $file,
                    ];
                }
            }
        }

        usort($hairs, static fn(array $a, array $b) => strcmp((string) $a['name'], (string) $b['name']));

        if (empty($hairs)) {
            $hairs = [
                ['id' => 'short', 'name' => 'Short', 'path' => 'hair/hair_short.glb'],
                ['id' => 'long', 'name' => 'Long', 'path' => 'hair/hair_long.glb'],
                ['id' => 'curly', 'name' => 'Curly', 'path' => 'hair/hair_curly.glb'],
            ];
        }

        return new JsonResponse($hairs);
    }

    #[Route('/extract/{mode}', name: 'api_avatar_extract', methods: ['POST'])]
    public function extractFeatures(string $mode, Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $url = $this->pythonServiceUrl . "/extract/{$mode}";
        $options = [];

        try {
            if ($mode === 'text') {
                $text = $request->request->get('text') ?? json_decode($request->getContent(), true)['text'] ?? '';
                $options['body'] = ['text' => $text];
            } else {
                /** @var UploadedFile $file */
                $file = $request->files->get('file');
                if (!$file) {
                    return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
                }

                $formData = new FormDataPart([
                    'file' => DataPart::fromPath($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType()),
                ]);
                
                $options['headers'] = $formData->getPreparedHeaders()->toArray();
                $options['body'] = $formData->bodyToIterable();
            }

            $response = $httpClient->request('POST', $url, $options);
            return new JsonResponse($response->toArray());
        } catch (\Exception $e) {
            // Fallback: Return a random/default avatar configuration if AI service is unavailable
            return new JsonResponse([
                'body_type' => 'male',
                'skin_tone' => 'medium',
                'hair_style' => 'short',
                'hair_path' => 'hair/hair_short.glb',
                'hair_color' => 'brown',
                'eye_color' => 'blue',
                'outfit' => 'outfit',
                'outfit_path' => 'outfits/outfit.glb',
                'expression' => 'neutral',
                'accessory' => 'none'
            ]);
        }
    }

    #[Route('/chat', name: 'api_avatar_chat', methods: ['POST'])]
    public function chat(\App\Service\Avatar\OllamaChatService $chatService, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $message = $request->request->get('message');
        if (!$message) {
            $data = json_decode($request->getContent(), true);
            $message = $data['message'] ?? null;
        }

        $voice = $request->files->get('voice');
        $image = $request->files->get('image');

        $result = $chatService->chat($user, $message, $voice, $image);
        return new JsonResponse($result);
    }

    #[Route('/save', name: 'api_avatar_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['params'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $avatar = $user->getGameAvatar() ?? new GameAvatar();
        $avatar->setUser($user);
        $avatar->setAvatarParams($data['params']);
        $avatar->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($avatar);
        $em->flush();

        return new JsonResponse(['status' => 'saved']);
    }

    #[Route('/studio', name: 'app_avatar_studio')]
    public function studio(): Response
    {
        return $this->render('avatar/studio.html.twig', [
            'studio_route' => 'app_avatar_studio',
            'generate_route' => 'api_avatar_extract', // Assuming this is the generation endpoint
            'latest' => null, // Placeholder or fetch latest avatar
            'rpm_subdomain' => 'innolearn',
            'avaturn_subdomain' => 'innolearn'
        ]);
    }
}
