<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class GroqService
{
    private $httpClient;
    private $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        #[Autowire('%env(GROQ_API_KEY)%')] string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    private function callGroq(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Groq API Key is not configured.');
        }

        $response = $this->httpClient->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => array_merge([
                'model' => 'llama-3.3-70b-versatile',
                'messages' => $messages,
                'temperature' => 0.7,
            ], $options)
        ]);

        return $response->toArray();
    }

    public function analyzeComplexity(string $title, string $description): string
    {
        $prompt = "Analyse le projet suivant et détermine son niveau de difficulté technique parmi : Débutant, Intermédiaire, Avancé. Réponds UNIQUEMENT par un seul mot.\n\nTitre : $title\nDescription : $description";

        try {
            $response = $this->callGroq([
                ['role' => 'user', 'content' => $prompt]
            ]);

            $result = $response['choices'][0]['message']['content'] ?? 'Intermédiaire';
            $result = trim($result, " . \n\r\t");
            $allowed = ['Débutant', 'Intermédiaire', 'Avancé'];

            foreach ($allowed as $level) {
                if (stripos($result, $level) !== false)
                    return $level;
            }
            return 'Intermédiaire';
        } catch (\Exception $e) {
            return 'Intermédiaire';
        }
    }

    public function generateQuestionsFromText(string $text): array
    {
        $prompt = "Génère 3-5 questions de quiz basées sur ce texte : $text. Réponds UNIQUEMENT en JSON sous forme de tableau d'objets avec les clés: question_text, type (toujours 'Choix multiple'), correct_answer, points.";

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Tu es un assistant pédagogique. Réponds UNIQUEMENT par un tableau JSON valide.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            $content = $response['choices'][0]['message']['content'] ?? '[]';
            // Extract JSON if there's markdown around it
            if (preg_match('/\[.*\]/s', $content, $matches)) {
                $content = $matches[0];
            }
            return json_decode($content, true) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getPedagogicalAnalysis(string $quizTitle, array $stats, int $questionCount): string
    {
        $prompt = "Analyse les résultats du quiz '$quizTitle': Pass: {$stats['pass']}, Fail: {$stats['fail']}, Questions: $questionCount. Donne un conseil pédagogique court.";

        $response = $this->callGroq([
            ['role' => 'user', 'content' => $prompt]
        ]);

        return $response['choices'][0]['message']['content'] ?? "Analyse indisponible.";
    }

    public function summarizeBook(string $text): string
    {
        $prompt = "En tant qu'assistant pédagogique InnoLearn, analyse le contenu suivant extrait d'un livre et donne un résumé concis (environ 200 mots) qui explique les concepts clés et les objectifs d'apprentissage abordés.\n\nContenu :\n" . mb_substr($text, 0, 15000);

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Tu es un assistant pédagogique InnoLearn d\'élite.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            return $response['choices'][0]['message']['content'] ?? "Désolé, l'IA n'a pas pu générer un résumé pour le moment.";
        } catch (\Exception $e) {
            return "Une erreur technique est survenue lors du résumé via Groq.";
        }
    }

    public function generateCareerAdvice(array $profile, array $peers, array $offers): array
    {
        $prompt = sprintf(
            "Analyse ce profil étudiant :\n- Domaine: %s\n- Compétences: %s\n- Niveau: %s\n\nBenchmark pairs: %s\nAttentes marché: %s\n\nRéponds UNIQUEMENT en JSON avec les clés: standing (int 0-100), skillGaps (array), actionPlan (array).",
            $profile['domaine'],
            $profile['competences'],
            $profile['niveau'],
            $peers['summary'] ?? '',
            $offers['summary'] ?? ''
        );

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Tu es un conseiller carrière expert. Réponds UNIQUEMENT en JSON.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            $content = $response['choices'][0]['message']['content'] ?? '{}';
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $content = $matches[0];
            }
            return json_decode($content, true) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function calculateMatchingScore(array $offer, array $candidate): array
    {
        $offerTitre = $offer['titre'] ?? 'Offre de stage';
        $candidateNom = $candidate['nom'] ?? 'Candidat';

        $prompt = "Analyse la compatibilité entre l'offre {$offerTitre} et le candidat {$candidateNom}. Réponds UNIQUEMENT en JSON: {score (int), explanation (string), strengths (array), weaknesses (array)}.";

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Réponds en JSON.'],
                ['role' => 'user', 'content' => $prompt]
            ]);
            $content = $response['choices'][0]['message']['content'] ?? '{}';
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $content = $matches[0];
            }
            $data = json_decode($content, true) ?: [];
            
            return array_merge([
                'score' => 0,
                'explanation' => 'Analyse indisponible',
                'strengths' => [],
                'weaknesses' => []
            ], $data);
        } catch (\Exception $e) {
            return [
                'score' => 0,
                'explanation' => "Erreur de connexion à l'IA: " . $e->getMessage(),
                'strengths' => [],
                'weaknesses' => []
            ];
        }
    }

    public function generateCoverLetterDraft(array $student, array $offer): string
    {
        $prompt = "Rédige une lettre de motivation pour {$student['nom']} pour le stage {$offer['titre']} chez {$offer['entreprise']}.";

        try {
            $response = $this->callGroq([['role' => 'user', 'content' => $prompt]]);
            return $response['choices'][0]['message']['content'] ?? "";
        } catch (\Exception $e) {
            return "Erreur lors de la génération via Groq.";
        }
    }

    public function generateInterviewQuestions(array $offer): array
    {
        $prompt = "Génère 5 questions d'entretien pertinentes pour le poste de '{$offer['titre']}' avec des conseils de réponse.
        Réponds UNIQUEMENT au format JSON structure comme ceci: 
        {\"questions\": [{\"q\": \"question...\", \"advice\": \"conseil...\"}]}";

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Tu es un expert en recrutement. Réponds uniquement en JSON sans texte additionnel.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            $content = $response['choices'][0]['message']['content'] ?? '';
            
            // Clean markdown if present
            $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
            
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['questions']) || !is_array($data['questions'])) {
                // Attempt to find JSON if AI added text around it
                if (preg_replace('/.*?(\{.*\})/s', '$1', $content, 1, $count) && $count > 0) {
                    $cleanedJson = preg_replace('/.*?(\{.*\})/s', '$1', $content);
                    $data = json_decode($cleanedJson, true);
                }
            }

            return $data['questions'] ?? $this->getFallbackQuestions($offer['titre']);
        } catch (\Exception $e) {
            return $this->getFallbackQuestions($offer['titre']);
        }
    }

    private function getFallbackQuestions(string $titre): array
    {
        return [
            ['q' => "Pouvez-vous nous présenter votre parcours et votre intérêt pour ce poste de $titre ?", 'advice' => "Soyez concis et mettez en avant vos compétences clés liées à l'offre."],
            ['q' => "Quelle est votre plus grande réalisation technique ou académique ?", 'advice' => "Décrivez une situation précise, l'action que vous avez menée et le résultat obtenu."],
            ['q' => "Comment gérez-vous une situation de stress ou un conflit d'équipe ?", 'advice' => "Montrez votre capacité de communication et votre esprit d'équipe."],
            ['q' => "Pourquoi avoir choisi InnoLearn pour postuler à ce stage ?", 'advice' => "Montrez que vous connaissez l'entreprise et ses valeurs."],
            ['q' => "Où vous voyez-vous dans deux ans ?", 'advice' => "Partagez vos ambitions tout en restant réaliste par rapport à l'apprentissage."]
        ];
    }

    public function analyzePageContent(string $content): string
    {
        $prompt = "Analyse et explique le contenu de cette page d'apprentissage de manière structurée et motivante : \n\n" . mb_substr($content, 0, 10000);

        try {
            $response = $this->callGroq([
                ['role' => 'system', 'content' => 'Tu es un tuteur pédagogique expert.'],
                ['role' => 'user', 'content' => $prompt]
            ]);

            return $response['choices'][0]['message']['content'] ?? "Désolé, je n'ai pas pu analyser la page.";
        } catch (\Exception $e) {
            return "Impossible de contacter l'Assistant pour le moment.";
        }
    }
}
