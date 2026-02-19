<?php

namespace App\Service;

use App\Repository\ProjectRepository;

class ChatBotService
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function generateResponse(string $message): string
    {
        $message = mb_strtolower(trim($message));

        // 1. Greet / Intro
        if ($this->matchPattern($message, ['bonjour', 'salut', 'hello', 'hey', 'coucou'])) {
            return "Salut ! Je suis **InnoBot**, ton assistant intelligent. Je peux t'aider à trouver des projets, t'expliquer des technologies ou te guider dans ton apprentissage. Que veux-tu savoir ?";
        }

        // 2. Identify Technology explanations
        if ($this->matchPattern($message, ['c\'est quoi', 'expliquer', 'definition', 'qu\'est-ce que'])) {
            return $this->handleTechDefinition($message);
        }

        // 3. Search for projects
        if ($this->matchPattern($message, ['projet', 'recherche', 'trouve', 'liste', 'quels'])) {
            return $this->handleProjectSearch($message);
        }

        // 4. Help / Navigation
        if ($this->matchPattern($message, ['aide', 'perdu', 'comment', 'marche', 'fonctionne'])) {
            return "Sur cette page, tu peux voir tous les projets collaboratifs d'InnoLearn. Tu peux filtrer par statut (recherche, en cours, terminé) ou utiliser la barre de recherche. L'IA a même analysé la difficulté pour toi ! Si tu cliques sur un projet, tu auras tous les détails.";
        }

        // 5. Tech Keywords check
        $techResponse = $this->handleTechDefinition($message, true);
        if ($techResponse !== "") {
            return $techResponse;
        }

        // Default fallback
        return "Je ne suis pas sûr de comprendre, mais je progresse chaque jour ! Tu peux me poser des questions sur les projets, les technos (Symfony, React, PHP...) ou me demander de l'aide pour choisir un sujet.";
    }

    private function matchPattern(string $message, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }
        return false;
    }

    private function handleTechDefinition(string $message, bool $silent = false): string
    {
        $knowledge = [
            'symfony' => "**Symfony** est un framework PHP puissant utilisé pour créer des applications web robustes. C'est le coeur d'InnoLearn !",
            'php' => "**PHP** est le langage de programmation " . 'côté serveur le plus utilisé pour le web.',
            'react' => "**React** est une bibliothèque JavaScript pour créer des interfaces utilisateur modernes et réactives.",
            'javascript' => "**JavaScript** est le langage qui rend le web interactif. Indispensable pour tes projets !",
            'sql' => "**SQL** est le langage utilisé pour communiquer avec les bases de données (comme MySQL).",
            'docker' => "**Docker** permet d'isoler tes applications dans des 'conteneurs' pour qu'elles fonctionnent partout pareil.",
            'api' => "Une **API** permet à deux logiciels de communiquer entre eux (ex: ton front-end parle à ton back-end).",
            'html' => "**HTML** est la structure de base de toutes les pages web.",
            'css' => "**CSS** s'occupe du style et de la beauté de tes sites web."
        ];

        foreach ($knowledge as $key => $desc) {
            if (str_contains($message, $key)) {
                return $desc;
            }
        }

        return $silent ? "" : "C'est une excellente question technique ! Malheureusement, je n'ai pas encore de définition précise pour cela dans ma base, mais je t'encourage à demander à tes tuteurs ou à consulter la doc officielle.";
    }

    private function handleProjectSearch(string $message): string
    {
        $projects = $this->projectRepository->findAll();
        $matches = [];
        
        // Find best matches based on keywords in title/desc
        foreach ($projects as $project) {
            if (str_contains(mb_strtolower($project->getTitle()), $message) || str_contains(mb_strtolower($project->getDescription()), $message)) {
                $matches[] = $project;
            }
        }

        if (count($matches) > 0) {
            $response = "J'ai trouvé **" . count($matches) . "** projet(s) qui pourraient t'intéresser :\n\n";
            $count = 0;
            foreach ($matches as $project) {
                if ($count >= 3) break; // Limit to 3 results
                $response .= "- **" . $project->getTitle() . "** (" . ($project->getDifficulty() ?? 'Intermédiaire') . ")\n";
                $count++;
            }
            return $response . "\nTu peux les voir directement dans la liste à gauche !";
        }

        return "Je n'ai pas trouvé de projet spécifique correspondant à ta demande pour le moment. Pourquoi ne pas explorer les projets 'Débutant' pour commencer ?";
    }
}
