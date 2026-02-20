<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AIService
{
    private $httpClient;
    private $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        #[Autowire('%env(GEMINI_API_KEY)%')] string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    public function listAvailableModels(): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API Key is not configured in .env');
        }

        $response = $this->httpClient->request('GET', 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $this->apiKey);
        return $response->toArray();
    }

    public function generateQuestionsFromText(string $text): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API Key is not configured in .env');
        }

        $prompt = <<<EOT
Tu es un assistant pédagogique. Génère 3 à 5 questions de quiz à partir du texte suivant.
Le format de réponse doit être strictement un tableau JSON d'objets.
Chaque objet doit avoir les clés suivantes:
- "question_text": le texte de la question.
- "type": toujours "Choix multiple" pour cet exemple.
- "correct_answer": la réponse correcte.
- "points": un nombre entier entre 1 et 5 selon la difficulté.

Texte:
$text

Réponse JSON (uniquement le tableau):
EOT;

        $configsToTry = [
            ['ver' => 'v1beta', 'model' => 'gemini-1.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.0-flash-lite'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.0-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-flash-lite-latest'],
            ['ver' => 'v1beta', 'model' => 'gemini-flash-latest'],
            ['ver' => 'v1beta', 'model' => 'gemini-pro-latest'],
        ];
        $lastError = '';
        $delay = 1500000; // Start with 1.5s

        foreach ($configsToTry as $config) {
            $ver = $config['ver'];
            $modelName = $config['model'];
            try {
                $response = $this->httpClient->request('POST', "https://generativelanguage.googleapis.com/$ver/models/$modelName:generateContent?key=" . $this->apiKey, [
                    'json' => [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 1024,
                        ]
                    ]
                ]);

                $content = $response->toArray();
                if (!isset($content['candidates'][0]['content']['parts'][0]['text'])) {
                    continue;
                }

                $jsonResponse = $content['candidates'][0]['content']['parts'][0]['text'];
                // Robust JSON cleaning: extract first [ and last ]
                if (preg_match('/\[.*\]/s', $jsonResponse, $matches)) {
                    $jsonResponse = $matches[0];
                }
                
                $data = json_decode($jsonResponse, true);
                if (is_array($data)) {
                    return $data;
                }
            } catch (\Exception $e) {
                if (method_exists($e, 'getResponse')) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    // 404 = Model not found, 503 = Overloaded, 429 = Rate limit
                    if (in_array($statusCode, [404, 429, 500, 502, 503, 504])) {
                        $lastError = "Modèle $modelName errored ($statusCode).";
                        
                        // Extra patience for rate limits
                        if ($statusCode === 429) {
                            sleep(2); // Wait 2s immediately
                        }
                        
                        usleep($delay);
                        $delay *= 2; // More aggressive backoff
                        continue; 
                    }
                    $errorBody = $e->getResponse()->getContent(false);
                    throw new \Exception("Erreur API ($statusCode): " . $errorBody);
                }
                $lastError = $e->getMessage();
            }
        }

        throw new \Exception("Échec de la génération après plusieurs tentatives. Dernière erreur: $lastError");
    }

    public function getPedagogicalAnalysis(string $quizTitle, array $stats, int $questionCount): string
    {
        if (empty($this->apiKey)) {
            return "L'IA est temporairement indisponible (clé API manquante).";
        }

        $total = $stats['pass'] + $stats['fail'];
        $prompt = sprintf(
            "En tant qu'expert pédagogique, analyse ces résultats de quiz pour le cours : '%s'.\n" .
            "Statistiques : %d étudiants ont participé. %d ont réussi (>=50%%) et %d ont échoué.\n" .
            "Le quiz contient %d questions. Donne un conseil court (3-4 phrases) en français pour aider l'enseignant à améliorer sa classe.",
            $quizTitle,
            $total,
            $stats['pass'],
            $stats['fail'],
            $questionCount
        );

        $configsToTry = [
            ['ver' => 'v1beta', 'model' => 'gemini-1.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.0-flash-lite'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.0-flash'],
        ];

        $delay = 1000000;
        foreach ($configsToTry as $config) {
            $ver = $config['ver'];
            $modelName = $config['model'];
            try {
                $response = $this->httpClient->request('POST', "https://generativelanguage.googleapis.com/$ver/models/$modelName:generateContent?key=" . $this->apiKey, [
                    'json' => [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]
                ]);

                $content = $response->toArray();
                if (isset($content['candidates'][0]['content']['parts'][0]['text'])) {
                    return $content['candidates'][0]['content']['parts'][0]['text'];
                }
            } catch (\Exception $e) {
                if (method_exists($e, 'getResponse') && $e->getResponse()->getStatusCode() === 429) {
                    sleep(1);
                }
                usleep($delay);
                $delay *= 1.8;
                continue;
            }
        }

        return "Désolé, l'IA n'a pas pu générer d'analyse pour le moment. Veuillez réessayer plus tard.";
    }

    public function summarizeBook(string $filePath): string
    {
        if (empty($this->apiKey)) {
            return "L'IA est temporairement indisponible.";
        }

        if (!file_exists($filePath)) {
            return "Erreur : Le fichier livre est introuvable.";
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = mb_substr($text, 0, 10000); // Limit to first 10k chars for efficiency
        } catch (\Exception $e) {
            return "Erreur lors de l'extraction du texte du PDF : " . $e->getMessage();
        }

        $prompt = "En tant qu'assistant pédagogique InnoLearn, analyse le contenu suivant extrait d'un livre et donne un résumé concis (environ 200 mots) qui explique les concepts clés et les objectifs d'apprentissage abordés.\n\nContenu :\n$text";

        $configsToTry = [
            ['ver' => 'v1beta', 'model' => 'gemini-1.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.5-flash'],
            ['ver' => 'v1beta', 'model' => 'gemini-2.0-flash-lite'],
        ];

        $delay = 1000000;
        foreach ($configsToTry as $config) {
            $ver = $config['ver'];
            $modelName = $config['model'];
            try {
                $response = $this->httpClient->request('POST', "https://generativelanguage.googleapis.com/$ver/models/$modelName:generateContent?key=" . $this->apiKey, [
                    'json' => [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]
                ]);

                $content = $response->toArray();
                if (isset($content['candidates'][0]['content']['parts'][0]['text'])) {
                    return $content['candidates'][0]['content']['parts'][0]['text'];
                }
            } catch (\Exception $e) {
                usleep($delay);
                $delay *= 2;
                continue;
            }
        }

        return "Désolé, l'IA n'a pas pu résumer le livre après plusieurs tentatives.";
    }
}
