<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AIService
{
    private $cache;
    private $groqService;

    public function __construct(
        CacheInterface $cache,
        GroqService $groqService
    ) {
        $this->cache = $cache;
        $this->groqService = $groqService;
    }

    public function generateQuestionsFromText(string $text): array
    {
        $cacheKey = 'ai_questions_v3_' . md5($text);

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($text) {
                $item->expiresAfter(86400); // 24 hours
                return $this->groqService->generateQuestionsFromText($text);
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getPedagogicalAnalysis(string $quizTitle, array $stats, int $questionCount): string
    {
        $total = $stats['pass'] + $stats['fail'];
        $cacheKey = 'quiz_analysis_v3_' . md5($quizTitle . $total . $questionCount);

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($quizTitle, $stats, $questionCount) {
                $item->expiresAfter(86400); // 24 hours
                return $this->groqService->getPedagogicalAnalysis($quizTitle, $stats, $questionCount);
            });
        } catch (\Exception $e) {
            return "Une erreur est survenue lors de l'analyse IA via Groq.";
        }
    }

    public function summarizeBook(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return "Erreur : Le fichier livre est introuvable.";
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = mb_substr($text, 0, 24000); // 24k chars for better context while staying within limits
            return $this->groqService->summarizeBook($text);
        } catch (\Exception $e) {
            return "Erreur lors de l'analyse du PDF : " . $e->getMessage();
        }
    }

    public function determineMatchingScore(array $offerData, array $candidateData): array
    {
        return $this->groqService->calculateMatchingScore($offerData, $candidateData);
    }

    public function generateCoverLetter(array $studentData, array $offerData): string
    {
        return $this->groqService->generateCoverLetterDraft($studentData, $offerData);
    }

    public function getInterviewPrep(array $offerData): array
    {
        return $this->groqService->generateInterviewQuestions($offerData);
    }

    public function analyzePageContent(string $content): string
    {
        return $this->groqService->analyzePageContent($content);
    }
}
