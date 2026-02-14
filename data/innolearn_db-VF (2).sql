-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 07 fév. 2026 à 13:49
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `innolearn_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `book`
--

CREATE TABLE `book` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `publier` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `book`
--

INSERT INTO `book` (`id`, `titre`, `author`, `description`, `publier`) VALUES
(1, 'Clean Code', 'Robert C. Martin', 'A fundamental book on software craftsmanship, providing rules and best practices for writing clean, maintainable code.', '2008-08-01'),
(2, 'The Pragmatic Programmer', 'Andrew Hunt & David Thomas', 'A classic guide for software developers, covering topics from personal responsibility to architectural techniques.', '1999-10-30'),
(3, 'Design Patterns', 'Gang of Four', 'The seminal work on design patterns in object-oriented software engineering.', '1994-10-21'),
(4, 'Refactoring', 'Martin Fowler', 'A comprehensive guide to improving the design of existing code without changing its external behavior.', '1999-07-08'),
(5, 'Introduction to Algorithms', 'Thomas H. Cormen', 'An in-depth guide to algorithms, covering a broad range of algorithms in depth.', '2009-07-31'),
(6, 'You Don\'t Know JS', 'Kyle Simpson', 'A deep dive into the core mechanisms of the JavaScript language.', '2015-12-27');

-- --------------------------------------------------------

--
-- Structure de la table `book_categorie`
--

CREATE TABLE `book_categorie` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `book_categorie`
--

INSERT INTO `book_categorie` (`id`, `titre`, `description`, `date`, `status`) VALUES
(1, 'aziz', 'mselmani', '2026-01-01', 'inactive'),
(3, 'final', 'check', '2021-01-01', 'inactive'),
(5, 'legend', 'new category', '2021-04-12', 'active'),
(6, 'test', '1 2', '2021-01-01', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `categorie_cours`
--

CREATE TABLE `categorie_cours` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `niveau` varchar(255) NOT NULL,
  `datepublication` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie_cours`
--

INSERT INTO `categorie_cours` (`id`, `titre`, `description`, `niveau`, `datepublication`) VALUES
(1, 'ReseauX', 'jjjjjjjjjjjjjjjjjjjjjj', 'Débutant', '2026-02-13 12:05:00'),
(2, 'Mobile', 'ffffffffffffffffffffffffffff', 'Avancé', '2026-02-19 12:08:00'),
(3, 'UML (use case)', 'cccccccccccccccccccccccccccccc', 'Intermédiaire', '2026-02-17 14:14:00'),
(4, 'Java / Web', 'jajajjajaveveveveveve', 'Débutant', '2026-02-11 22:38:00'),
(5, 'Psychologie', 'DeveloppementPersonnelle', 'Expert', '2026-02-28 10:47:00');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type_media` varchar(50) NOT NULL,
  `media_url` varchar(500) NOT NULL,
  `duree` int(11) NOT NULL,
  `niveau` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL,
  `enseignant` varchar(255) NOT NULL,
  `categorie_cours_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `nom`, `description`, `slug`, `type_media`, `media_url`, `duree`, `niveau`, `date_creation`, `enseignant`, `categorie_cours_id`) VALUES
(1, 'Symfony v 6.4', 'jejejejejej', 'masterclass', 'video_intro', 'https://youtu.be/i_jgWZItCGI?si=wOGuDA2qkOJnsolX', 10, 'Débutant', '2026-02-06 15:07:28', 'Vous', 1),
(2, 'Analyse', 'kekekeke', 'masterclass', 'video_intro', 'https://youtu.be/KjRoTBtP3L8?si=9_As_A-bc0M092j0', 10, 'Intermédiaire', '2026-02-06 15:09:06', 'Vous', 1),
(5, 'finance', 'llll', 'masterclass', 'video_intro', 'https://youtu.be/i_jgWZItCGI?si=wOGuDA2qkOJnsolX', 16, 'Avancé', '2026-02-06 15:47:40', 'Vous', 1),
(11, 'web/java', 'jejejeje', 'masterclass', 'video_intro', 'https://youtu.be/hJHvdBlSxug?si=En6HNpRv1VPjm7VG', 20, 'Avancé', '2026-02-06 23:30:08', 'Vous', 1),
(13, 'Angular', 'Fullstack dev', 'masterclass', 'video_intro', 'https://www.youtube.com/watch?v=sOeIBKM25mc', 10, 'Intermédiaire', '2026-02-07 09:08:22', 'Vous', 1);

-- --------------------------------------------------------

--
-- Structure de la table `cours_categorie`
--

CREATE TABLE `cours_categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `slug` varchar(255) NOT NULL,
  `datecreation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `depots`
--

CREATE TABLE `depots` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_at` datetime NOT NULL,
  `project_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `depots`
--

INSERT INTO `depots` (`id`, `title`, `description`, `type`, `file_path`, `file_size`, `file_type`, `uploaded_at`, `project_id`, `student_name`, `download_count`) VALUES
(1, 'ahmedd', 'mohamed', 'document', 'backlog---feuille-1-69860364e79d5.pdf', '87892', 'application/pdf', '2026-02-06 15:06:13', 1, 'Étudiant InnoLearn', 0);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260201222511', '2026-02-01 23:26:36', 145),
('DoctrineMigrations\\Version20260202CreateFormulaire', '2026-02-06 14:57:09', 194),
('DoctrineMigrations\\Version20260204CreateQuestion', '2026-02-06 14:57:09', 143);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `type_evenement` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu` varchar(50) NOT NULL,
  `capacite` int(11) NOT NULL,
  `statut` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id`, `titre`, `description`, `type_evenement`, `date_debut`, `date_fin`, `lieu`, `capacite`, `statut`) VALUES
(10, 'TEDx events', 'In the spirit of TED’s mission, “ideas worth spreading,” the TEDx program helps communities, organizations and individuals produce TED-style events at the local level. TEDx events are planned and coordinated independently, on a community-by-community basis, under a free license from TED.', 'workshop', '2027-04-09 06:00:00', '2027-04-09 10:00:00', 'جامعة تونس المنار, نهج البشير سالم بلخيرية, كولزي', 200, 'actif'),
(11, 'Conference Cyber Security', 'Conference Cyber SecurityConference Cyber SecurityConference Cyber SecurityConference Cyber Security', 'conference', '2026-02-20 11:38:00', '2026-02-25 11:38:00', 'ddddzz', 600, 'actif'),
(12, 'ssssssssssssssssss', 'ssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', 'conference', '2026-02-01 12:19:00', '2026-02-03 12:19:00', 'Tunis, Gouvernorat Tunis, Tunisie', 1, 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `formulaire`
--

CREATE TABLE `formulaire` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `temps_limite` int(11) DEFAULT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formulaire`
--

INSERT INTO `formulaire` (`id`, `titre`, `description`, `temps_limite`, `category`) VALUES
(2, 'test', 'lola is a good quiz you know?', 15, 'linuxx'),
(3, 'matha', 'mecanique math test', 40, 'student'),
(5, 'mini quiz', 'mini quiz to test', 7, 'java'),
(7, 'sabaoui', 'saboui is a bad kid', 3, 'female'),
(9, 'bomba', 'bomba bomba bomba', 3, 'bomba'),
(10, 'intooooezezr', 'zeeeeeeeeeeeeeeeeeeeeeeeeerze', 40, 'UI/UX');

-- --------------------------------------------------------

--
-- Structure de la table `inscrit_event`
--

CREATE TABLE `inscrit_event` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_inscrit` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'En attente',
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscrit_event`
--

INSERT INTO `inscrit_event` (`id`, `name`, `email`, `date_inscrit`, `status`, `event_id`) VALUES
(2, 'belhassen', 'benazzoun.belhassen@gmail.com', '2026-02-07 11:35:07', 'Confirmé', 11),
(3, 'Fashion Boutiques', 'benazzoun.belhassen@gmail.com', '2026-02-07 11:47:45', 'Confirmé', 11);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offrestage`
--

CREATE TABLE `offrestage` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `entreprise` varchar(255) NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `domaine` varchar(255) NOT NULL,
  `competences` longtext NOT NULL,
  `duree` int(11) NOT NULL,
  `date_publication` datetime NOT NULL,
  `statut` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `offrestage`
--

INSERT INTO `offrestage` (`id`, `titre`, `description`, `entreprise`, `lieu`, `domaine`, `competences`, `duree`, `date_publication`, `statut`) VALUES
(1, 'frre', 'fefere', 'fefe', 'fefrer', 'erferffyfhhj66666', 'erferfefe', 3, '2024-03-01 00:00:00', 'ouverte'),
(2, 'Stage Développeur Symfony', 'Rejoignez notre équipe pour développer des applications web innovantes.', 'InnoLearn', 'Tunis', 'Informatique', 'Symfony, PHP, MySQL', 4, '2021-09-10 00:00:00', 'ouverte'),
(3, 'Stage Data Scientist', 'Analyse de données massives et mise en place de modèles prédictifs.', 'Matrix Corp', 'Ariana', 'Informatique', 'Python, SQL, Machine Learning', 6, '2021-09-15 00:00:00', 'ouverte'),
(4, 'Stage Designer UI/UX', 'Conception d\'interfaces intuitives et esthétiques pour nos clients.', 'Creative Agency', 'Sousse', 'Design', 'Figma, Adobe XD, User Research', 3, '2021-09-18 00:00:00', 'ouverte'),
(5, 'Stage Marketing Digital', 'Pilotage de campagnes publicitaires et gestion des réseaux sociaux.', 'Growth Boosters', 'Tunis', 'Marketing', 'SEO, SEM, Social Media', 4, '2021-09-20 00:00:00', 'ouverte'),
(6, 'Stage DevOps Cloud', 'Automatisation des déploiements et gestion de l\'infrastructure cloud.', 'Tech Cloud', 'Sfax', 'Informatique', 'Docker, Kubernetes, AWS', 6, '2021-09-22 00:00:00', 'ouverte'),
(7, 'Développeur Fullstack React/Node', 'Rejoignez notre équipe Azure pour travailler sur des solutions cloud innovantes.', 'Microsoft', 'Issy-les-Moulineaux', 'Informatique', 'React, Node.js, TypeScript, Docker', 6, '2026-02-04 00:00:00', 'ouverte'),
(8, 'Data Scientist - IA Générative', 'Travaillez sur l\'intégration de l\'IA générative dans nos produits phares.', 'Google', 'Paris', 'Data Science', 'Python, PyTorch, LLMs, NLP', 6, '2026-02-01 00:00:00', 'ouverte'),
(9, 'UX/UI Designer Junior', 'Participez au design des futures interfaces iOS au sein de notre centre de R&D.', 'Apple', 'Paris', 'Design', 'Figma, Adobe XD, Design Systems, Prototyping', 4, '2026-01-27 00:00:00', 'ouverte'),
(10, 'Game Designer - Assassin\'s Creed', 'Contribuez à la création d\'environnements immersifs pour nos prochaines productions AAA.', 'Ubisoft', 'Montpellier', 'Jeux Vidéo', 'Level Design, Unity, C#, Storytelling', 6, '2026-01-22 00:00:00', 'ouverte'),
(11, 'Ingénieur DevOps / Cloud', 'Optimisez les infrastructures cloud pour nos clients stratégiques en Europe.', 'Amazon AWS', 'Clichy', 'Cloud Computing', 'AWS, Kubernetes, Terraform, CI/CD', 6, '2026-01-17 00:00:00', 'ouverte'),
(12, 'Analyste Cybersécurité', 'Contribuez à la protection des infrastructures critiques de nos clients.', 'Thales', 'La Défense', 'Sécurité', 'Pentesting, SIEM, Réseaux, SOC', 6, '2026-01-06 00:00:00', 'ouverte'),
(13, 'Développeur Mobile Flutter', 'Rejoignez la team mobile pour améliorer l\'expérience de covoiturage de millions d\'utilisateurs.', 'BlaBlaCar', 'Paris', 'Mobile', 'Dart, Flutter, Firebase, REST API', 5, '2025-12-23 00:00:00', 'ouverte'),
(14, 'Product Owner Junior', 'Accompagnez l\'évolution de notre plateforme e-commerce sportive.', 'Decathlon', 'Lille', 'Management', 'Agilité, Scrum, User Stories, Roadmap', 6, '2025-12-06 00:00:00', 'ouverte'),
(15, 'Développeur Fullstack React/Node', 'Rejoignez notre équipe Azure pour travailler sur des solutions cloud innovantes.', 'Microsoft', 'Issy-les-Moulineaux', 'Informatique', 'React, Node.js, TypeScript, Docker', 6, '2026-02-04 00:00:00', 'ouverte'),
(16, 'Data Scientist - IA Générative', 'Travaillez sur l\'intégration de l\'IA générative dans nos produits phares.', 'Google', 'Paris', 'Data Science', 'Python, PyTorch, LLMs, NLP', 6, '2026-02-01 00:00:00', 'ouverte'),
(17, 'UX/UI Designer Junior', 'Participez au design des futures interfaces iOS au sein de notre centre de R&D.', 'Apple', 'Paris', 'Design', 'Figma, Adobe XD, Design Systems, Prototyping', 4, '2026-01-27 00:00:00', 'ouverte'),
(18, 'Game Designer - Assassin\'s Creed', 'Contribuez à la création d\'environnements immersifs pour nos prochaines productions AAA.', 'Ubisoft', 'Montpellier', 'Jeux Vidéo', 'Level Design, Unity, C#, Storytelling', 6, '2026-01-22 00:00:00', 'ouverte'),
(19, 'Ingénieur DevOps / Cloud', 'Optimisez les infrastructures cloud pour nos clients stratégiques en Europe.', 'Amazon AWS', 'Clichy', 'Cloud Computing', 'AWS, Kubernetes, Terraform, CI/CD', 6, '2026-01-17 00:00:00', 'ouverte'),
(20, 'Analyste Cybersécurité', 'Contribuez à la protection des infrastructures critiques de nos clients.', 'Thales', 'La Défense', 'Sécurité', 'Pentesting, SIEM, Réseaux, SOC', 6, '2026-01-06 00:00:00', 'ouverte'),
(21, 'Développeur Mobile Flutter', 'Rejoignez la team mobile pour améliorer l\'expérience de covoiturage de millions d\'utilisateurs.', 'BlaBlaCar', 'Paris', 'Mobile', 'Dart, Flutter, Firebase, REST API', 5, '2025-12-23 00:00:00', 'ouverte'),
(22, 'Product Owner Junior', 'Accompagnez l\'évolution de notre plateforme e-commerce sportive.', 'Decathlon', 'Lille', 'Management', 'Agilité, Scrum, User Stories, Roadmap', 6, '2025-12-06 00:00:00', 'ouverte'),
(23, 'dcxxssssssssssss', 'dfffffffffffffffffffffffffffffffffffdfdsdfs', 'sddfffffffffff', 'dddsqq dfsdfs', 'technologie', 'dddddddddddddddddddddddf dfddddddddddffds dsfdsfsdfs', 3, '2026-02-11 00:00:00', 'ouverte');

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `status` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`id`, `title`, `description`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'html', 'chapitre 1', 'active', '2026-02-02', '2025-12-11', '2026-02-02 09:53:13', NULL),
(2, 'java', 'chapitre 3', 'active', '2026-02-12', '2025-12-11', '2026-02-02 09:53:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `formulaire_id` int(11) NOT NULL,
  `question_text` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `question`
--

INSERT INTO `question` (`id`, `formulaire_id`, `question_text`, `type`, `correct_answer`, `points`) VALUES
(3, 2, 'how old are you?', 'number', '10', 3),
(4, 2, 'yes or no', 'true_false', 'false', 2),
(5, 2, 'test2', 'number', '5', 2),
(6, 3, '5+5\r\n4\r\n10\r\n20', 'multiple_choice', '10', 1),
(7, 3, 'text', 'true_false', 'false', 3),
(8, 7, 'idiot?', 'true_false', 'true', 10),
(9, 9, 'bomba?', 'multiple_choice', 'bomba', 4);

-- --------------------------------------------------------

--
-- Structure de la table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `quiz`
--

INSERT INTO `quiz` (`id`, `title`, `description`, `duration`, `difficulty`, `created_at`) VALUES
(1, 'Introduction à Symfony', 'Testez vos connaissances sur les bases du framework Symfony (Routing, Controllers, Services).', 20, 'Débutant', '2026-02-03 19:44:06'),
(2, 'Maîtrise de Twig', 'Des variables aux filtres, maîtrisez le moteur de template Twig pour vos vues Symfony.', 15, 'Intermédiaire', '2026-02-03 19:44:07'),
(3, 'Doctrine ORM Avancé', 'Relations complexes, DQL, Query Builder et optimisation de requêtes SQL.', 30, 'Avancé', '2026-02-03 19:44:07'),
(4, 'UI/UX Design Masterclass', 'Principes fondamentaux du design centré utilisateur, prototypage et accessibilité.', 25, 'Débutant', '2026-02-03 19:44:07'),
(5, 'Marketing Digital Essentials', 'SEO, SEA, stratégies de contenu et analyse de données pour la croissance web.', 20, 'Intermédiaire', '2026-02-03 19:44:07'),
(6, 'JavaScript Modern (ES6+)', 'Promesses, async/await, modules et nouveautés du langage JavaScript.', 15, 'Intermédiaire', '2026-02-03 19:44:07');

-- --------------------------------------------------------

--
-- Structure de la table `stagecondidature`
--

CREATE TABLE `stagecondidature` (
  `id` int(11) NOT NULL,
  `type_request` varchar(255) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `domaine` varchar(255) NOT NULL,
  `competences` longtext NOT NULL,
  `cv` varchar(255) NOT NULL,
  `lettre_motivation` longtext NOT NULL,
  `date_publication` date NOT NULL,
  `statut` varchar(255) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_offre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stagecondidature`
--

INSERT INTO `stagecondidature` (`id`, `type_request`, `titre`, `description`, `domaine`, `competences`, `cv`, `lettre_motivation`, `date_publication`, `statut`, `id_etudiant`, `id_offre`) VALUES
(1, 'offre', 'qsdqdq', 'sqdqsdqs', 'qsdqqsddsq', 'sqdqsdqsdqs', 'qsdqsdsdqdsq', 'qsdqdqsdsq', '2021-09-18', 'en_attente', 6, 5),
(2, 'offre', 'Développeur Fullstack React/Node', 'Je recherche un stage de 6 mois en tant que développeur fullstack.', 'Informatique', 'React, Node.js, JavaScript, CSS, HTML', 'cv_1.pdf', 'Je suis passionné par le développement web...', '2021-09-12', 'en_attente', 1, 1),
(3, 'offre', 'Analyste de Données Senior', 'Expert en Python et R, je cherche un stage en Data Science.', 'Statistiques', 'Python, R, SQL, Machine Learning', 'cv_2.pdf', 'Ma rigueur et mon esprit analytique sont mes atouts...', '2021-09-14', 'en_attente', 2, 2),
(4, 'demande', 'Designer Graphique & UI', 'Créatif et motivé, je cherche à mettre en pratique mes compétences en design.', 'Design', 'Photoshop, Illustrator, Figma, Canva', 'cv_3.pdf', 'Le design est pour moi un moyen d\'expression...', '2021-09-16', 'en_attente', 3, 0),
(5, 'offre', 'Chef de Projet Marketing', 'Étudiant en école de commerce, spécialisé en marketing digital.', 'Marketing', 'SEO, SEM, Google Analytics, Social Media', 'cv_4.pdf', 'J\'ai hâte de contribuer à votre équipe marketing...', '2021-09-18', 'en_attente', 4, 4),
(6, 'demande', 'Ingénieur Réseaux & Sécurité', 'Je suis à la recherche d\'un stage technique en cybersécurité.', 'Informatique', 'Cisco, Firewall, Linux, Wireshark', 'cv_5.pdf', 'La sécurité des réseaux est un enjeu majeur...', '2021-09-20', 'en_attente', 5, 0),
(7, 'offre', 'Stage Marketing Digital', 'Pilotage de campagnes publicitaires et gestion des réseaux sociaux.', 'Marketing', 'SEO, SEM, Social Media', 'cv_default.pdf', 'Motivation pour l\'offre : Stage Marketing Digital', '2026-02-06', 'en_attente', 1, 5),
(8, 'offre', 'Développeur Fullstack React/Node', 'Rejoignez notre équipe Azure pour travailler sur des solutions cloud innovantes.', 'Informatique', 'React, Node.js, TypeScript, Docker', 'cv_default.pdf', 'sqdqsdsqdsqdqsqdsdsdqssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', '2026-02-06', 'acceptée', 1, 7),
(9, 'offre', 'dcxxssssssssssss', 'dfffffffffffffffffffffffffffffffffffdfdsdfs', 'technologie', 'dddddddddddddddddddddddf dfddddddddddffds dsfdsfsdfs', 'cv_default.pdf', 'zeazezaezaezazzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', '2026-02-06', 'refusée', 1, 23),
(11, 'demande', 'mousamiiii', 'zzzzzzzzzzzzzzzzzzzzzzzzzzz', 'eezez', 'zezezezzzz ezezeze eze zez', 'zezezez', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeezzzz', '2026-02-06', 'en_attente', 1, 0),
(12, 'demande', 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 'jjjjjjjjjjjjjjjjjjjjjjjj', 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 'workshop-intgration-templates-69863cc5b4b71.pdf', 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', '2026-02-06', 'refusée', 1, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `book_categorie`
--
ALTER TABLE `book_categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `categorie_cours`
--
ALTER TABLE `categorie_cours`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FDCA8C9C464839DA` (`categorie_cours_id`);

--
-- Index pour la table `cours_categorie`
--
ALTER TABLE `cours_categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `depots`
--
ALTER TABLE `depots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3353C6EC166D1F9C` (`project_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `formulaire`
--
ALTER TABLE `formulaire`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `inscrit_event`
--
ALTER TABLE `inscrit_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_13E341C071F7E88B` (`event_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `offrestage`
--
ALTER TABLE `offrestage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FORMULAIRE` (`formulaire_id`);

--
-- Index pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stagecondidature`
--
ALTER TABLE `stagecondidature`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `book`
--
ALTER TABLE `book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `book_categorie`
--
ALTER TABLE `book_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `categorie_cours`
--
ALTER TABLE `categorie_cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `cours_categorie`
--
ALTER TABLE `cours_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `depots`
--
ALTER TABLE `depots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `formulaire`
--
ALTER TABLE `formulaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `inscrit_event`
--
ALTER TABLE `inscrit_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `offrestage`
--
ALTER TABLE `offrestage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `stagecondidature`
--
ALTER TABLE `stagecondidature`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `FK_FDCA8C9C464839DA` FOREIGN KEY (`categorie_cours_id`) REFERENCES `categorie_cours` (`id`);

--
-- Contraintes pour la table `depots`
--
ALTER TABLE `depots`
  ADD CONSTRAINT `FK_3353C6EC166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscrit_event`
--
ALTER TABLE `inscrit_event`
  ADD CONSTRAINT `FK_13E341C071F7E88B` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`);

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_FORMULAIRE` FOREIGN KEY (`formulaire_id`) REFERENCES `formulaire` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
