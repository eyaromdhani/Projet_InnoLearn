<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\ExperienceRepository;
use App\Repository\OffreStageRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CareerService
{
    private $profileRepository;
    private $experienceRepository;
    private $offreRepository;
    private $coursRepository;
    private $bookRepository;
    private $projectRepository;
    private $geminiService;
    private $groqService;
    private $cache;

    public function __construct(
        ProfileRepository $profileRepository,
        ExperienceRepository $experienceRepository,
        OffreStageRepository $offreRepository,
        \App\Repository\CoursRepository $coursRepository,
        \App\Repository\BookRepository $bookRepository,
        \App\Repository\ProjectRepository $projectRepository,
        GeminiService $geminiService,
        GroqService $groqService,
        CacheInterface $cache
    ) {
        $this->profileRepository = $profileRepository;
        $this->experienceRepository = $experienceRepository;
        $this->offreRepository = $offreRepository;
        $this->coursRepository = $coursRepository;
        $this->bookRepository = $bookRepository;
        $this->projectRepository = $projectRepository;
        $this->geminiService = $geminiService;
        $this->groqService = $groqService;
        $this->cache = $cache;
    }

    public function getRecommendations(User $user): array
    {
        $profile = $this->profileRepository->findOneBy(['user' => $user]);
        if (!$profile) {
            return [
                'error' => "Profil non trouvé. Veuillez d'abord remplir votre profil.",
                'standing' => 0,
                'skillGaps' => [],
                'actionPlan' => []
            ];
        }

        $experiences = $this->experienceRepository->findBy(['user' => $user]);
        $expStrings = array_map(fn($e) => $e->getEtablissement() . " - " . $e->getDomaine() . " (" . $e->getType() . ")", $experiences);

        $profileData = [
            'domaine' => $profile->getDomaine(),
            'competences' => $profile->getCompetences(),
            'niveau' => $profile->getNiveauAcademique(),
            'experiences' => implode(', ', $expStrings)
        ];

        // Benchmark info
        $peersCount = $this->profileRepository->count(['domaine' => $profile->getDomaine()]);
        $peersData = [
            'summary' => "Il y a $peersCount autres étudiants dans le domaine {$profile->getDomaine()}."
        ];

        // Market info
        $matchingOffers = $this->offreRepository->findBy(['domaine' => $profile->getDomaine()], null, 5);
        $offerTitles = array_map(fn($o) => $o->getTitre() . " (" . $o->getCompetences() . ")", $matchingOffers);
        $offersData = [
            'summary' => "Offres récentes : " . implode(' | ', $offerTitles)
        ];

        // Cache recommendations for 24 hours based on profile data hash
        $cacheKey = 'career_rec_' . $user->getId() . '_' . md5(json_encode($profileData) . json_encode($offersData));

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($profileData, $peersData, $offersData) {
                $item->expiresAfter(86400); // 24 hours

                try {
                    // Step 1: Try Gemini (Standard)
                    return $this->geminiService->generateCareerAdvice($profileData, $peersData, $offersData);
                } catch (\Exception $e) {
                    // Step 2: Try Groq (Fast Fallback) if Gemini fails
                    return $this->groqService->generateCareerAdvice($profileData, $peersData, $offersData);
                }
            });
        } catch (\Exception $e) {
            // Step 3: Local Expert System (Guaranteed)
            return $this->getLocalAnalysis($profileData);
        }
    }

    /**
     * Local Expert System: Provides analysis using keyword matching.
     * Guaranteed performance and availability.
     */
    private function getLocalAnalysis(array $profileData): array
    {
        $domain = strtolower($profileData['domaine']);
        $skills = strtolower($profileData['competences']);
        $level = strtolower($profileData['niveau']);

        // Dictionary of Skills and Plan by Domain
        $knowledge = [
            'genie logiciel' => [
                'skills' => ['php', 'symfony', 'java', 'springboot', 'angular', 'react', 'mysql', 'docker', 'git'],
                'plan' => [
                    'Approfondir les patterns d\'architecture (MVC, Microservices).',
                    'Réaliser un projet full-stack avec Symfony et React.',
                    'Obtenir une certification sur les outils DevOps (Docker, CI/CD).'
                ]
            ],
            'design' => [
                'skills' => ['ui', 'ux', 'figma', 'adobe xd', 'photoshop', 'illustrator', 'prototypage'],
                'plan' => [
                    'Travailler sur le Design System d\'un projet complexe.',
                    'Réaliser des tests utilisateurs pour valider une expérience UX.',
                    'Se former au prototypage avancé sur Figma.'
                ]
            ],
            'ia' => [
                'skills' => ['python', 'tensorflow', 'pytorch', 'machine learning', 'deep learning', 'pandas', 'nlp'],
                'plan' => [
                    'Participer à des compétitions Kaggle pour tester ses modèles.',
                    'Développer une mini-application utilisant des LLMs ou le NLP.',
                    'Suivre une formation avancée sur le Deep Learning.'
                ]
            ],
            'business intelligence' => [
                'skills' => ['power bi', 'tableau', 'sql', 'python', 'etl', 'data warehouse', 'excel'],
                'plan' => [
                    'Maîtriser les outils de Data Visualisation avancés.',
                    'Travailler sur des cas réels de manipulation de Big Data.',
                    'Se former au langage DAX pour Power BI.'
                ]
            ],
            'psychologie' => [
                'skills' => ['statistique', 'tests psychométriques', 'écoute active', 'empathie', 'analyse comportementale', 'clinique'],
                'plan' => [
                    'Se spécialiser dans une approche thérapeutique spécifique.',
                    'Participer à des séminaires sur la psychologie clinique moderne.',
                    'Effectuer un stage pratique dans une structure spécialisée.'
                ]
            ]
        ];

        // Flexible matching for domain
        $data = null;
        foreach ($knowledge as $key => $val) {
            if (str_contains($domain, $key) || str_contains($key, $domain)) {
                $data = $val;
                break;
            }
        }

        // Generic fallback if not found
        if (!$data) {
            return [
                'standing' => 60,
                'skillGaps' => ['Spécialisation à définir', 'Veille sectorielle'],
                'actionPlan' => [
                    'Compléter les certifications liées à votre domaine.',
                    'Développer un portfolio de projets concrets.',
                    'Améliorer vos soft skills et votre réseau professionnel.'
                ],
                'is_fallback' => true
            ];
        }

        // Calculate Standing (Score)
        $score = 50;
        if (str_contains($level, 'master'))
            $score += 15;
        if (str_contains($level, 'cycle'))
            $score += 10;

        $userSkills = array_map('trim', explode(',', $skills));
        $matchCount = 0;
        $missing = [];

        foreach ($data['skills'] as $required) {
            $found = false;
            foreach ($userSkills as $uSkill) {
                if (str_contains($uSkill, $required)) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $matchCount++;
            } else {
                $missing[] = ucfirst($required);
            }
        }

        $score += ($matchCount * 5);
        $score = min($score, 92);

        return [
            'standing' => $score,
            'skillGaps' => count($missing) > 0 ? array_slice($missing, 0, 4) : ['Communication avancée'],
            'actionPlan' => $data['plan'],
            'is_fallback' => true
        ];
    }

    public function getPlatformRecommendations(User $user): array
    {
        $profile = $this->profileRepository->findOneBy(['user' => $user]);
        if (!$profile)
            return [];

        $domain = $profile->getDomaine();
        $keywords = array_map('trim', explode(' ', strtolower($domain)));

        // 1. Recommendation for Course (Formation)
        $course = $this->coursRepository->createQueryBuilder('c')
            ->leftJoin('c.categorieCours', 'cat')
            ->where('LOWER(cat.titre) LIKE :domain OR LOWER(c.nom) LIKE :domain OR LOWER(c.description) LIKE :domain')
            ->setParameter('domain', '%' . strtolower($domain) . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // 2. Recommendation for Book (Livre)
        $book = $this->bookRepository->createQueryBuilder('b')
            ->where('LOWER(b.titre) LIKE :domain OR LOWER(b.description) LIKE :domain')
            ->setParameter('domain', '%' . strtolower($domain) . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // 3. Recommendation for Project (Projet)
        $project = $this->projectRepository->createQueryBuilder('p')
            ->where('(LOWER(p.title) LIKE :domain OR LOWER(p.description) LIKE :domain) AND p.status = :active')
            ->setParameter('domain', '%' . strtolower($domain) . '%')
            ->setParameter('active', 'active')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'course' => $course,
            'book' => $book,
            'project' => $project
        ];
    }
}
