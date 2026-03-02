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

        // 1. Salutations
        if ($this->matchPattern($message, ['bonjour', 'salut', 'hello', 'hey', 'coucou', 'bonsoir', 'hi', 'yo'])) {
            return "Salut ! Je suis **InnoBot**, ton assistant intelligent. Je peux t'aider avec :\n\n" .
                "- Trouver des projets adaptes a ton niveau\n" .
                "- Expliquer des technologies (Symfony, React, Python...)\n" .
                "- Comparer des outils (React vs Vue, SQL vs NoSQL...)\n" .
                "- Conseiller un parcours carriere\n" .
                "- Guider sur la plateforme InnoLearn\n\n" .
                "Que veux-tu savoir ?";
        }

        // 2. Remerciements
        if ($this->matchPattern($message, ['merci', 'thanks', 'thx', 'cool', 'super', 'parfait', 'top'])) {
            return "Avec plaisir ! N'hesite pas si tu as d'autres questions. Je suis toujours la pour t'aider !";
        }

        // 3. Au revoir
        if ($this->matchPattern($message, ['bye', 'au revoir', 'ciao', 'bonne nuit'])) {
            return "A bientot ! Bonne continuation dans tes projets. N'hesite pas a revenir si tu as besoin d'aide !";
        }

        // 4. Comparaisons tech
        if ($this->matchPattern($message, ['vs', 'versus', 'comparaison', 'comparer', 'lequel choisir'])) {
            $comparison = $this->handleComparison($message);
            if ($comparison)
                return $comparison;
        }

        // 5. Conseils parcours carriere
        if ($this->matchPattern($message, ['carriere', 'metier', 'devenir', 'parcours', 'orientation', 'job'])) {
            return $this->handleCareerAdvice($message);
        }

        // 6. Questions sur la plateforme InnoLearn
        if ($this->matchPattern($message, ['plateforme', 'innolearn', 'inscription', 'compte', 'profil', 'deposer', 'depot', 'quiz', 'note', 'evaluation', 'cours', 'stage'])) {
            return $this->handlePlatformHelp($message);
        }

        // 7. Definitions technologies
        if ($this->matchPattern($message, ["c'est quoi", 'expliquer', 'definition', "qu'est-ce que"])) {
            return $this->handleTechDefinition($message);
        }

        // 8. Recherche de projets
        if ($this->matchPattern($message, ['projet', 'recherche', 'trouve', 'liste', 'quels', 'suggestion', 'recommande'])) {
            return $this->handleProjectSearch($message);
        }

        // 9. Conseils projet / methodologie
        if ($this->matchPattern($message, ['conseil', 'astuce', 'comment faire', 'commencer', 'methode', 'organiser', 'equipe', 'group'])) {
            return $this->handleProjectAdvice($message);
        }

        // 10. Motivation / Encouragement
        if ($this->matchPattern($message, ['stress', 'difficile', 'dur', 'peur', 'perdu', 'bloque', 'abandonner', 'nul', 'comprends pas'])) {
            return $this->handleMotivation();
        }

        // 11. Aide / Navigation
        if ($this->matchPattern($message, ['aide', 'help', 'comment', 'marche', 'fonctionne', 'navigation', 'utiliser'])) {
            return "Sur cette page, tu peux :\n\n" .
                "- Explorer les projets collaboratifs\n" .
                "- Filtrer par statut (en cours, termine...)\n" .
                "- Voir la difficulte calculee par l'IA\n" .
                "- Postuler ou consulter les details d'un projet\n" .
                "- Me poser des questions sur les technos !\n\n" .
                "Essaie de taper le nom d'une technologie (ex: Python, React)";
        }

        // 12. Detection directe par mot-cle tech
        $techResponse = $this->handleTechDefinition($message, true);
        if ($techResponse !== "") {
            return $techResponse;
        }

        // 13. Detection par mot-cle plateforme
        $platformResponse = $this->handlePlatformHelp($message, true);
        if ($platformResponse !== "") {
            return $platformResponse;
        }

        // Fallback
        return "Je ne suis pas sur de comprendre ta question, mais tu peux essayer :\n\n" .
            "- **\"C'est quoi Python ?\"** pour une definition\n" .
            "- **\"React vs Vue\"** pour une comparaison\n" .
            "- **\"Projet debutant\"** pour des suggestions\n" .
            "- **\"Devenir developpeur\"** pour des conseils carriere\n" .
            "- **\"Comment deposer\"** pour l'aide plateforme\n\n" .
            "Je suis la pour t'aider !";
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

    // ====================================
    // BASE DE CONNAISSANCES TECHNOLOGIES
    // ====================================
    private function handleTechDefinition(string $message, bool $silent = false): string
    {
        $knowledge = [
            // --- Langages ---
            'symfony' => "**Symfony** est un framework PHP puissant pour creer des applications web professionnelles. C'est le coeur d'InnoLearn !\n\n**Niveau :** Intermediaire\n**Utilise pour :** Applications web, API REST, e-commerce\n**Apprendre :** symfony.com/doc",
            'php' => "**PHP** est le langage serveur le plus utilise pour le web (80% des sites). WordPress, Laravel et Symfony sont tous en PHP.\n\n**Niveau :** Debutant-Intermediaire\n**Forces :** Grande communaute, facile a deployer, enorme ecosysteme",
            'python' => "**Python** est un langage polyvalent, ideal pour l'IA, le data science et l'automatisation. Sa syntaxe simple le rend parfait pour debuter.\n\n**Niveau :** Debutant\n**Utilise pour :** IA, Machine Learning, scripts, web (Django/Flask)\n**Apprendre :** python.org",
            'javascript' => "**JavaScript** est LE langage du web. Il tourne dans tous les navigateurs et aussi cote serveur avec Node.js.\n\n**Niveau :** Debutant-Intermediaire\n**Utilise pour :** Sites interactifs, apps web, mobile (React Native)\n**Frameworks :** React, Vue, Angular, Next.js",
            'java' => "**Java** est un langage robuste et portable, tres utilise en entreprise et pour les applications Android.\n\n**Niveau :** Intermediaire\n**Utilise pour :** Applications d'entreprise, Android, microservices\n**Outils :** Spring Boot, Maven, IntelliJ IDEA",
            'typescript' => "**TypeScript** = JavaScript + les types ! Il rend le code plus fiable et maintenable pour les gros projets.\n\n**Niveau :** Intermediaire\n**Utilise avec :** Angular, React, Node.js, Next.js\n**Avantage :** Detection d'erreurs avant l'execution",
            'c++' => "**C++** est un langage performant utilise pour les jeux video, les systemes embarques et les applications critiques.\n\n**Niveau :** Avance\n**Utilise pour :** Jeux (Unreal Engine), systemes d'exploitation, IoT",
            'c#' => "**C#** est le langage de Microsoft, utilise pour les jeux Unity, les applications Windows et le web avec ASP.NET.\n\n**Niveau :** Intermediaire\n**Utilise pour :** Jeux video (Unity), apps Windows, web (.NET)",
            'ruby' => "**Ruby** est un langage elegant connu pour le framework **Ruby on Rails**, ideal pour creer des apps web rapidement.\n\n**Niveau :** Intermediaire\n**Forces :** Productivite elevee, convention over configuration",
            'go' => "**Go (Golang)** est le langage de Google, concu pour la performance et la concurrence. Parfait pour les microservices.\n\n**Niveau :** Intermediaire-Avance\n**Utilise pour :** API haute performance, microservices, outils CLI",
            'rust' => "**Rust** est un langage systeme ultra-performant et securise en memoire, alternative moderne a C++.\n\n**Niveau :** Avance\n**Utilise pour :** Systemes, WebAssembly, outils performants",
            'kotlin' => "**Kotlin** est le langage officiel pour Android, developpe par JetBrains. Plus moderne et concis que Java.\n\n**Niveau :** Intermediaire\n**Utilise pour :** Applications Android, backend (Spring)",
            'swift' => "**Swift** est le langage d'Apple pour iOS et macOS. Rapide, sur et moderne.\n\n**Niveau :** Intermediaire\n**Utilise pour :** Applications iPhone/iPad/Mac",
            'dart' => "**Dart** est le langage de Google utilise avec **Flutter** pour creer des apps mobiles multiplateformes.\n\n**Niveau :** Debutant-Intermediaire\n**Utilise avec :** Flutter (iOS + Android + Web)",

            // --- Frameworks & Bibliotheques ---
            'react' => "**React** est la bibliotheque JavaScript de Facebook pour creer des interfaces utilisateur modernes et reactives.\n\n**Niveau :** Intermediaire\n**Forces :** Composants reutilisables, Virtual DOM, enorme ecosysteme\n**Utilise par :** Facebook, Instagram, Netflix, Airbnb",
            'vue' => "**Vue.js** est un framework JavaScript progressif, facile a apprendre et tres elegant.\n\n**Niveau :** Debutant-Intermediaire\n**Forces :** Courbe d'apprentissage douce, documentation excellente\n**Utilise par :** Alibaba, GitLab, Nintendo",
            'angular' => "**Angular** est le framework JavaScript de Google, complet et structure pour les grandes applications.\n\n**Niveau :** Intermediaire-Avance\n**Forces :** Tout-en-un, TypeScript natif, architecture rigoureuse\n**Utilise par :** Google, Microsoft, Samsung",
            'nextjs' => "**Next.js** est un framework React pour le rendu cote serveur (SSR) et les sites statiques.\n\n**Niveau :** Intermediaire\n**Forces :** SEO, performance, deploiement facile (Vercel)",
            'laravel' => "**Laravel** est le framework PHP le plus populaire, avec une syntaxe elegante et des outils puissants.\n\n**Niveau :** Intermediaire\n**Forces :** Eloquent ORM, Blade templates, artisan CLI",
            'django' => "**Django** est un framework Python complet pour creer des sites web rapidement (\"batteries included\").\n\n**Niveau :** Intermediaire\n**Forces :** Admin auto-generee, ORM puissant, securite integree",
            'flask' => "**Flask** est un micro-framework Python leger et flexible, parfait pour les API et petites apps.\n\n**Niveau :** Debutant\n**Forces :** Minimaliste, facile a apprendre, extensible",
            'spring' => "**Spring Boot** est le framework Java de reference pour les applications d'entreprise et microservices.\n\n**Niveau :** Avance\n**Utilise pour :** API REST, microservices, applications bancaires",
            'flutter' => "**Flutter** est le SDK de Google pour creer des apps mobiles natives iOS et Android avec un seul code (Dart).\n\n**Niveau :** Intermediaire\n**Forces :** Hot reload, widgets magnifiques, multiplateforme",
            'tailwind' => "**Tailwind CSS** est un framework CSS utilitaire qui permet de styliser directement dans le HTML.\n\n**Niveau :** Debutant\n**Forces :** Rapidite de developpement, design coherent, customisable",
            'bootstrap' => "**Bootstrap** est le framework CSS le plus populaire pour creer des sites responsives rapidement.\n\n**Niveau :** Debutant\n**Forces :** Grille responsive, composants prets a l'usage, themes",
            'nodejs' => "**Node.js** permet d'executer JavaScript cote serveur. Parfait pour les API temps reel.\n\n**Niveau :** Intermediaire\n**Forces :** Non-bloquant, npm (millions de packages), full-stack JS",
            'node' => "**Node.js** permet d'executer JavaScript cote serveur. Parfait pour les API temps reel.\n\n**Niveau :** Intermediaire\n**Forces :** Non-bloquant, npm (millions de packages), full-stack JS",
            'express' => "**Express.js** est le framework Node.js minimaliste de reference pour creer des API REST.\n\n**Niveau :** Debutant-Intermediaire\n**Forces :** Simple, flexible, middleware, grand ecosysteme",

            // --- Outils & Infrastructure ---
            'git' => "**Git** est le systeme de controle de version le plus utilise au monde. Indispensable pour tout developpeur !\n\n**Commandes essentielles :**\n- git add . : Ajouter les modifications\n- git commit -m \"message\" : Sauvegarder\n- git push : Envoyer sur GitHub\n- git pull : Recuperer les changements",
            'github' => "**GitHub** est la plateforme de collaboration pour heberger du code avec Git. C'est ton portfolio en tant que developpeur !\n\n**Fonctionnalites :** Repositories, Pull Requests, Issues, Actions CI/CD\n**Conseil :** Cree-toi un profil GitHub des maintenant !",
            'docker' => "**Docker** permet d'isoler tes applications dans des conteneurs pour qu'elles fonctionnent partout pareil.\n\n**Niveau :** Intermediaire-Avance\n**Utilise pour :** Deploiement, microservices, CI/CD\n**Commande cle :** docker-compose up",
            'kubernetes' => "**Kubernetes (K8s)** est l'orchestrateur de conteneurs Docker pour gerer des applications a grande echelle.\n\n**Niveau :** Avance\n**Utilise par :** Google, Spotify, Airbnb\n**Conseil :** Maitrise d'abord Docker avant K8s",
            'sql' => "**SQL** est le langage pour interroger les bases de donnees relationnelles (MySQL, PostgreSQL, SQLite).\n\n**Commandes essentielles :**\n- SELECT * FROM users : Lire\n- INSERT INTO : Creer\n- UPDATE : Modifier\n- DELETE : Supprimer",
            'mysql' => "**MySQL** est le systeme de base de donnees relationnelle le plus populaire, utilise par InnoLearn !\n\n**Niveau :** Debutant-Intermediaire\n**Outils :** phpMyAdmin, MySQL Workbench, DBeaver",
            'mongodb' => "**MongoDB** est une base de donnees NoSQL qui stocke les donnees en documents JSON.\n\n**Niveau :** Intermediaire\n**Forces :** Flexible, scalable, ideal pour les donnees non structurees\n**Utilise par :** Uber, eBay, Adobe",
            'postgresql' => "**PostgreSQL** est la base de donnees relationnelle open-source la plus avancee.\n\n**Niveau :** Intermediaire\n**Forces :** Conformite SQL, extensions, JSON support, performance",
            'api' => "Une **API** (Application Programming Interface) permet a deux logiciels de communiquer entre eux.\n\n**Types :** REST, GraphQL, SOAP\n**Exemple :** Ton frontend React appelle une API Symfony pour recuperer des donnees\n**Outil de test :** Postman",
            'html' => "**HTML** (HyperText Markup Language) est le squelette de toutes les pages web.\n\n**Niveau :** Debutant\n**Balises essentielles :** div, h1, p, a, img, form\n**Version actuelle :** HTML5",
            'css' => "**CSS** (Cascading Style Sheets) gere le style et la beaute de tes sites web.\n\n**Niveau :** Debutant\n**Concepts cles :** Flexbox, Grid, animations, media queries\n**Frameworks :** Bootstrap, Tailwind CSS, Bulma",
            'json' => "**JSON** (JavaScript Object Notation) est le format standard pour echanger des donnees entre applications.\n\n**Exemple :** {\"nom\": \"Ahmed\", \"age\": 22}\n**Utilise partout :** APIs, fichiers de config, bases NoSQL",
            'graphql' => "**GraphQL** est une alternative aux API REST, developpee par Facebook. Tu demandes exactement les donnees dont tu as besoin.\n\n**Niveau :** Intermediaire-Avance\n**Avantage :** Pas de sur-fetching ni sous-fetching de donnees",
            'linux' => "**Linux** est le systeme d'exploitation open-source qui fait tourner la majorite des serveurs web dans le monde.\n\n**Niveau :** Debutant-Intermediaire\n**Distributions :** Ubuntu, Debian, CentOS, Fedora\n**Commandes :** cd, ls, mkdir, chmod, apt",
            'vscode' => "**VS Code** est l'editeur de code le plus populaire au monde (par Microsoft), leger et ultra-extensible.\n\n**Extensions essentielles :** ESLint, Prettier, GitLens, PHP Intelephense\n**Raccourcis :** Ctrl+P (fichier), Ctrl+Shift+P (commandes)",
            'figma' => "**Figma** est l'outil de design collaboratif pour creer des maquettes et prototypes d'interfaces.\n\n**Niveau :** Debutant\n**Utilise pour :** UI/UX design, wireframes, prototypes\n**Gratuit pour les etudiants !**",
            'postman' => "**Postman** est l'outil de reference pour tester et documenter tes API REST.\n\n**Niveau :** Debutant\n**Fonctionnalites :** Requetes GET/POST/PUT/DELETE, tests automatises, collections",

            // --- Intelligence Artificielle ---
            'intelligence artificielle' => "L'**Intelligence Artificielle (IA)** permet aux machines d'apprendre et de prendre des decisions. C'est le domaine le plus demande aujourd'hui !\n\n**Sous-domaines :** Machine Learning, Deep Learning, NLP, Computer Vision\n**Langages :** Python (TensorFlow, PyTorch)\n**Niveau :** Avance",
            'machine learning' => "Le **Machine Learning** est une branche de l'IA ou les machines apprennent a partir de donnees.\n\n**Types :** Supervise, Non-supervise, Renforcement\n**Outils :** scikit-learn, TensorFlow, PyTorch\n**Niveau :** Avance",
        ];

        foreach ($knowledge as $key => $desc) {
            if (str_contains($message, $key)) {
                return $desc;
            }
        }

        return $silent ? "" : "Bonne question technique ! Je n'ai pas encore cette techno dans ma base de connaissances, mais je te recommande de consulter la documentation officielle ou de demander a tes enseignants.";
    }

    // ====================================
    // COMPARAISONS TECHNOLOGIQUES
    // ====================================
    private function handleComparison(string $message): ?string
    {
        $comparisons = [
            [
                'techs' => ['react', 'vue'],
                'response' => "**React vs Vue.js**\n\n| Critere | React | Vue.js |\n|---|---|---|\n| Difficulte | Intermediaire | Debutant-Intermediaire |\n| Cree par | Facebook | Evan You |\n| Approche | Bibliotheque flexible | Framework progressif |\n| Syntaxe | JSX | Templates HTML |\n| Communaute | Tres large | En croissance |\n| Emplois | Plus d'offres | Populaire en Asie/Europe |\n\n**Mon conseil :** Vue pour debuter, React pour le marche de l'emploi."
            ],
            [
                'techs' => ['react', 'angular'],
                'response' => "**React vs Angular**\n\n| Critere | React | Angular |\n|---|---|---|\n| Type | Bibliotheque | Framework complet |\n| Langage | JavaScript/JSX | TypeScript |\n| Courbe | Moyenne | Raide |\n| Entreprises | Facebook, Netflix | Google, Microsoft |\n| Taille app | Petites a grandes | Grandes apps |\n\n**Mon conseil :** React pour la flexibilite, Angular pour les projets structures d'entreprise."
            ],
            [
                'techs' => ['sql', 'nosql'],
                'response' => "**SQL vs NoSQL**\n\n| Critere | SQL | NoSQL |\n|---|---|---|\n| Structure | Tables relationnelles | Documents/Cle-valeur |\n| Exemples | MySQL, PostgreSQL | MongoDB, Redis |\n| Schema | Fixe et strict | Flexible |\n| Scalabilite | Verticale | Horizontale |\n| Ideal pour | Donnees structurees | Donnees variables |\n\n**Mon conseil :** SQL pour commencer, NoSQL pour les donnees flexibles."
            ],
            [
                'techs' => ['mysql', 'postgresql'],
                'response' => "**MySQL vs PostgreSQL**\n\n| Critere | MySQL | PostgreSQL |\n|---|---|---|\n| Facilite | Plus simple | Plus puissant |\n| Performance | Lecture rapide | Ecriture complexe |\n| JSON | Basique | Excellent |\n| Utilisation | Web, CMS | Data, entreprise |\n\n**Mon conseil :** MySQL pour les projets web classiques, PostgreSQL pour les projets complexes."
            ],
            [
                'techs' => ['php', 'python'],
                'response' => "**PHP vs Python**\n\n| Critere | PHP | Python |\n|---|---|---|\n| Domaine | Web exclusivement | Polyvalent |\n| Web | Laravel, Symfony | Django, Flask |\n| Syntaxe | Plus verbose | Plus elegante |\n| IA/Data | Non adapte | Excellent |\n| Emplois web | Tres nombreux | En croissance |\n\n**Mon conseil :** PHP si tu veux faire du web, Python si tu vises l'IA."
            ],
            [
                'techs' => ['laravel', 'symfony'],
                'response' => "**Laravel vs Symfony**\n\n| Critere | Laravel | Symfony |\n|---|---|---|\n| Facilite | Plus facile | Plus structure |\n| Approche | Convention | Configuration |\n| ORM | Eloquent | Doctrine |\n| Templates | Blade | Twig |\n| Communaute | Tres large | Professionnelle |\n\n**Mon conseil :** Laravel pour la rapidite, Symfony pour les projets d'entreprise (comme InnoLearn !)."
            ],
            [
                'techs' => ['flutter', 'react native'],
                'response' => "**Flutter vs React Native**\n\n| Critere | Flutter | React Native |\n|---|---|---|\n| Langage | Dart | JavaScript |\n| Performance | Excellente | Bonne |\n| UI | Widgets custom | Composants natifs |\n| Hot Reload | Oui | Oui |\n| Cree par | Google | Facebook |\n\n**Mon conseil :** Flutter pour la beaute de l'UI, React Native si tu connais deja React."
            ],
            [
                'techs' => ['docker', 'kubernetes'],
                'response' => "**Docker vs Kubernetes**\n\n| Critere | Docker | Kubernetes |\n|---|---|---|\n| Role | Creer des conteneurs | Orchestrer des conteneurs |\n| Complexite | Intermediaire | Avance |\n| Utilisation | Dev local, CI/CD | Production, scaling |\n\n**Mon conseil :** Apprends d'abord Docker, puis Kubernetes quand tu geres plusieurs conteneurs."
            ],
            [
                'techs' => ['java', 'kotlin'],
                'response' => "**Java vs Kotlin**\n\n| Critere | Java | Kotlin |\n|---|---|---|\n| Syntaxe | Verbose | Concise |\n| Null Safety | Non | Oui |\n| Android | Historique | Officiel (Google) |\n| Interoperabilite | -- | 100% avec Java |\n\n**Mon conseil :** Kotlin pour Android, Java pour le backend entreprise."
            ],
        ];

        foreach ($comparisons as $comparison) {
            $hasBoth = true;
            foreach ($comparison['techs'] as $tech) {
                if (!str_contains($message, $tech)) {
                    $hasBoth = false;
                    break;
                }
            }
            if ($hasBoth)
                return $comparison['response'];
        }

        return null;
    }

    // ====================================
    // AIDE PLATEFORME INNOLEARN
    // ====================================
    private function handlePlatformHelp(string $message, bool $silent = false): string
    {
        $platformKnowledge = [
            'inscription' => "**Inscription sur InnoLearn**\n\nPour t'inscrire sur InnoLearn :\n1. Clique sur **S'inscrire** sur la page d'accueil\n2. Remplis le formulaire avec ton email universitaire\n3. Choisis ton role (Etudiant / Enseignant)\n4. Confirme ton compte par email\n\nTu pourras ensuite acceder a tous les projets et cours !",
            'profil' => "**Ton Profil InnoLearn**\n\nDans ton profil, tu peux :\n- Modifier tes informations personnelles\n- Ajouter tes competences et experiences\n- Telecharger ton CV\n- Voir ton historique de projets et depots\n\nAccede via le menu **Carriere > Mon Profil**.",
            'depot' => "**Deposer un fichier**\n\nPour deposer un fichier sur un projet :\n1. Va sur la page du projet\n2. Clique sur **Ajouter un depot**\n3. Selectionne ton fichier (PDF, ZIP, etc.)\n4. Ajoute un commentaire decrivant ton travail\n5. Valide !\n\nTu peux deposer plusieurs fichiers par projet.",
            'deposer' => "**Deposer un fichier**\n\nPour deposer un fichier sur un projet :\n1. Va sur la page du projet\n2. Clique sur **Ajouter un depot**\n3. Selectionne ton fichier\n4. Valide !\n\nTu peux voir tes depots dans la section **Mes Depots**.",
            'quiz' => "**Les Quiz InnoLearn**\n\nLes quiz te permettent de tester tes connaissances !\n- Generes par l'IA a partir des cours\n- Correction automatique avec score\n- Analyse pedagogique de tes resultats\n- Accessibles depuis le menu **Quiz**\n\nChaque quiz est adapte au contenu du cours !",
            'cours' => "**Les Cours InnoLearn**\n\nTu peux acceder aux cours depuis le menu **Cours** :\n- Organises par categories\n- Contenu riche avec texte, images et videos\n- Quiz de verification associes\n- Suivi de progression\n\nExplore les categories pour trouver ce qui t'interesse !",
            'stage' => "**Les Stages InnoLearn**\n\nTu peux rechercher des stages depuis le menu **Stages** :\n- Offres publiees par les recruteurs\n- Filtres par domaine, lieu et duree\n- Candidature en ligne directement\n- Suivi du statut de ta candidature\n\nConsulte aussi le menu **Carriere** pour completer ton profil !",
            'note' => "**Les Notes et Evaluations**\n\nTes notes sont calculees automatiquement a partir :\n- Des quiz que tu passes\n- Des depots evalues par les enseignants\n- De ta participation aux projets\n\nConsulte tes resultats dans ta section **Carriere**.",
            'evaluation' => "**Systeme d'Evaluation**\n\nInnoLearn evalue tes projets sur :\n- La qualite technique du code\n- Le respect des delais\n- La collaboration en equipe\n- Les depots reguliers\n\nLes enseignants peuvent aussi laisser des commentaires personnalises.",
        ];

        foreach ($platformKnowledge as $key => $desc) {
            if (str_contains($message, $key)) {
                return $desc;
            }
        }

        return $silent ? "" : "Pour naviguer sur InnoLearn, utilise le menu lateral. Tu peux acceder aux **Projets**, **Cours**, **Quiz**, **Stages** et ton **Profil**. N'hesite pas a me demander plus de details !";
    }

    // ====================================
    // CONSEILS PROJETS & METHODOLOGIE
    // ====================================
    private function handleProjectAdvice(string $message): string
    {
        if ($this->matchPattern($message, ['equipe', 'group', 'collabor'])) {
            return "**Travailler en equipe**\n\n1. Utilisez **Git** pour partager le code\n2. Repartissez les taches clairement (front, back, design)\n3. Communiquez regulierement (Discord, Slack)\n4. Faites des reunions courtes mais regulieres\n5. Documentez votre travail\n\n**Astuce :** Utilisez un Kanban (Trello, Notion) pour suivre l'avancement !";
        }

        if ($this->matchPattern($message, ['commencer', 'premier'])) {
            return "**Commencer ton premier projet**\n\n1. Choisis un sujet qui te passionne\n2. Definis le perimetre (ne vois pas trop grand !)\n3. Prepare ton environnement (VS Code, Git)\n4. Commence par le plus simple (maquette, base de donnees)\n5. Fais des commits reguliers\n6. Teste souvent ton code\n\n**Conseil :** Un projet Debutant est parfait pour se lancer !";
        }

        if ($this->matchPattern($message, ['organiser', 'methode', 'gestion'])) {
            return "**Organiser ton projet**\n\nMethode recommandee :\n1. **Phase 1 - Analyse** (1-2 jours) : Comprendre les besoins\n2. **Phase 2 - Design** (1-2 jours) : Maquettes Figma\n3. **Phase 3 - Developpement** : Sprints d'une semaine\n4. **Phase 4 - Tests** : Tester chaque fonctionnalite\n5. **Phase 5 - Deploiement** : Mise en ligne\n\n**Astuce :** Utilise la methode Agile avec des sprints courts !";
        }

        return "**Conseils pour tes projets :**\n\n- Choisis un projet adapte a ton niveau actuel\n- Planifie avant de coder (maquettes, schema BDD)\n- Fais des commits Git reguliers\n- Teste ton code souvent\n- Collabore avec d'autres etudiants\n- Documente ton travail\n\nTu veux des suggestions de projets par niveau ?";
    }

    // ====================================
    // CONSEILS CARRIERE
    // ====================================
    private function handleCareerAdvice(string $message): string
    {
        $careers = [
            'frontend' => "**Developpeur Frontend**\n\n**Role :** Creer l'interface utilisateur des sites/apps\n**Technologies :** HTML, CSS, JavaScript, React/Vue/Angular\n**Salaire debutant :** 30-38K EUR/an\n**Competences cles :** UI/UX, responsive design, accessibilite\n\n**Parcours recommande :**\n1. HTML/CSS - 2. JavaScript - 3. React ou Vue - 4. TypeScript",
            'backend' => "**Developpeur Backend**\n\n**Role :** Gerer la logique serveur, les API et les bases de donnees\n**Technologies :** PHP/Symfony, Python/Django, Java/Spring, Node.js\n**Salaire debutant :** 32-40K EUR/an\n**Competences cles :** API REST, bases de donnees, securite\n\n**Parcours recommande :**\n1. PHP ou Python - 2. SQL - 3. Framework (Symfony/Django) - 4. API REST",
            'fullstack' => "**Developpeur Fullstack**\n\n**Role :** Maitriser le front ET le back !\n**Technologies :** React + Node.js, ou Vue + Symfony\n**Salaire debutant :** 35-42K EUR/an\n**Competences cles :** Polyvalence, architecture, DevOps basics\n\n**Parcours recommande :**\n1. Frontend basics - 2. Backend basics - 3. Projet full-stack - 4. DevOps",
            'devops' => "**Ingenieur DevOps**\n\n**Role :** Automatiser le deploiement et gerer l'infrastructure\n**Technologies :** Docker, Kubernetes, CI/CD, Linux, AWS/Azure\n**Salaire debutant :** 38-48K EUR/an\n**Competences cles :** Automatisation, monitoring, cloud\n\n**Parcours recommande :**\n1. Linux - 2. Docker - 3. CI/CD - 4. Kubernetes - 5. Cloud",
            'data' => "**Data Scientist / Analyste**\n\n**Role :** Analyser les donnees et creer des modeles predictifs\n**Technologies :** Python, R, SQL, TensorFlow, Power BI\n**Salaire debutant :** 35-45K EUR/an\n**Competences cles :** Statistiques, Machine Learning, visualisation\n\n**Parcours recommande :**\n1. Python - 2. SQL - 3. Pandas/NumPy - 4. Machine Learning",
            'mobile' => "**Developpeur Mobile**\n\n**Role :** Creer des applications iOS et Android\n**Technologies :** Flutter/Dart, React Native, Swift, Kotlin\n**Salaire debutant :** 33-40K EUR/an\n**Competences cles :** UX mobile, performance, stores\n\n**Parcours recommande :**\n1. JavaScript ou Dart - 2. React Native ou Flutter - 3. APIs - 4. Publication",
        ];

        foreach ($careers as $key => $desc) {
            if (str_contains($message, $key)) {
                return $desc;
            }
        }

        return "**Parcours Carriere en Tech**\n\nVoici les metiers les plus demandes :\n\n" .
            "- **Frontend** : tape \"carriere frontend\"\n" .
            "- **Backend** : tape \"carriere backend\"\n" .
            "- **Fullstack** : tape \"devenir fullstack\"\n" .
            "- **DevOps** : tape \"carriere devops\"\n" .
            "- **Data Science** : tape \"metier data\"\n" .
            "- **Mobile** : tape \"devenir mobile\"\n\n" .
            "Tape le nom d'un metier pour en savoir plus !";
    }

    // ====================================
    // MOTIVATION & ENCOURAGEMENT
    // ====================================
    private function handleMotivation(): string
    {
        $messages = [
            "**Tu peux y arriver !**\n\nChaque developpeur a ete debutant un jour. Les erreurs font partie de l'apprentissage.\n\n\"Le seul echec, c'est d'arreter d'essayer.\"\n\nConseils :\n- Fais des petites etapes (un fichier a la fois)\n- Demande de l'aide a tes camarades ou enseignants\n- Prends des pauses regulieres\n- Celebre tes petites victoires !",
            "**Chaque bug resolu te rend plus fort !**\n\nLe developpement, c'est 90% de debugging et 10% de code. C'est normal de galerer !\n\nAstuces :\n- Relis ton code ligne par ligne\n- Utilise console.log() ou dump() pour debugger\n- Cherche l'erreur sur Stack Overflow\n- Fais une pause et reviens avec un regard neuf",
            "**N'abandonne jamais !**\n\nLes meilleurs developpeurs sont ceux qui n'ont jamais abandonne face aux bugs.\n\n\"Tout expert etait un jour un debutant.\" - Helen Hayes\n\nTu es sur la bonne voie. Continue !",
        ];

        return $messages[array_rand($messages)];
    }

    // ====================================
    // RECHERCHE DE PROJETS
    // ====================================
    private function handleProjectSearch(string $message): string
    {
        // Recherche par niveau
        if ($this->matchPattern($message, ['debutant', 'facile', 'simple', 'commencer'])) {
            return $this->findProjectsByDifficulty('Debutant');
        }
        if ($this->matchPattern($message, ['intermediaire', 'moyen'])) {
            return $this->findProjectsByDifficulty('Intermediaire');
        }
        if ($this->matchPattern($message, ['avance', 'expert', 'difficile', 'complexe'])) {
            return $this->findProjectsByDifficulty('Avance');
        }

        // Recherche generale
        $projects = $this->projectRepository->findAll();
        $matches = [];

        foreach ($projects as $project) {
            $title = mb_strtolower($project->getTitle());
            $desc = mb_strtolower($project->getDescription());
            if (str_contains($title, $message) || str_contains($desc, $message)) {
                $matches[] = $project;
            }
        }

        if (count($matches) > 0) {
            $response = "J'ai trouve **" . count($matches) . "** projet(s) :\n\n";
            $count = 0;
            foreach ($matches as $project) {
                if ($count >= 5)
                    break;
                $difficulty = $project->getDifficulty() ?? 'Non defini';
                $response .= "- **" . $project->getTitle() . "** (" . $difficulty . ")\n";
                $count++;
            }
            return $response . "\nClique sur un projet dans la liste pour voir les details !";
        }

        return "Je n'ai pas trouve de projet correspondant. Essaie :\n- **\"Projet debutant\"** pour des projets faciles\n- **\"Projet avance\"** pour des projets complexes\n\nOu explore directement la liste des projets ci-dessus !";
    }

    private function findProjectsByDifficulty(string $difficulty): string
    {
        $projects = $this->projectRepository->findAll();
        $filtered = [];

        foreach ($projects as $project) {
            if ($project->getDifficulty() === $difficulty) {
                $filtered[] = $project;
            }
        }

        if (count($filtered) > 0) {
            $response = "**Projets $difficulty** (" . count($filtered) . " disponibles) :\n\n";
            $count = 0;
            foreach ($filtered as $project) {
                if ($count >= 5)
                    break;
                $status = $project->getStatus() ?? 'inconnu';
                $response .= "- **" . $project->getTitle() . "** (Statut: $status)\n";
                $count++;
            }
            return $response . "\nClique sur un projet pour plus de details !";
        }

        return "Aucun projet **$difficulty** n'est disponible pour le moment. Reste a l'affut, de nouveaux projets sont ajoutes regulierement !";
    }
}
