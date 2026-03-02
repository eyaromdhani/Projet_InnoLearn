<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GeminiService
{
    private $groqService;
    private $cache;

    public function __construct(
        GroqService $groqService,
        CacheInterface $cache
    ) {
        $this->groqService = $groqService;
        $this->cache = $cache;
    }

    public function analyzeComplexity(string $title, string $description): string
    {
        return $this->groqService->analyzeComplexity($title, $description);
    }

    public function generateCareerAdvice(array $profileData, array $peersData, array $offersData): array
    {
        $cacheKey = 'career_advice_groq_' . md5(json_encode([$profileData, $peersData, $offersData]));

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($profileData, $peersData, $offersData) {
                $item->expiresAfter(86400); // 1 day
                return $this->groqService->generateCareerAdvice($profileData, $peersData, $offersData);
            });
        } catch (\Exception $e) {
            return [
                'standing' => 70,
                'skillGaps' => ['Erreur IA'],
                'actionPlan' => ['Réessayez plus tard']
            ];
        }
    }
}
