<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206150108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Initialize Schema
        $this->addSql("CREATE TABLE IF NOT EXISTS `project` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `description` longtext NOT NULL,
          `status` varchar(50) NOT NULL,
          `start_date` date NOT NULL,
          `end_date` date DEFAULT NULL,
          `created_at` datetime NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->addSql("CREATE TABLE IF NOT EXISTS `depots` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `description` longtext DEFAULT NULL,
          `type` varchar(50) NOT NULL,
          `file_path` varchar(255) NOT NULL,
          `file_size` varchar(50) DEFAULT NULL,
          `file_type` varchar(100) DEFAULT NULL,
          `uploaded_at` datetime NOT NULL,
          `project_id` int(11) NOT NULL,
          `student_name` varchar(100) NOT NULL,
          `download_count` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`),
          KEY `IDX_3353C6EC166D1F9C` (`project_id`),
          CONSTRAINT `FK_3353C6EC166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->addSql("CREATE TABLE IF NOT EXISTS `formulaire` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `titre` varchar(255) NOT NULL,
          `description` varchar(255) NOT NULL,
          `temps_limite` int(11) DEFAULT NULL,
          `category` varchar(255) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->addSql("CREATE TABLE IF NOT EXISTS `question` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `formulaire_id` int(11) NOT NULL,
          `question_text` varchar(255) NOT NULL,
          `type` varchar(255) NOT NULL,
          `correct_answer` varchar(255) NOT NULL,
          `points` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `IDX_FORMULAIRE` (`formulaire_id`),
          CONSTRAINT `FK_FORMULAIRE` FOREIGN KEY (`formulaire_id`) REFERENCES `formulaire` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Project Data
        $this->addSql("INSERT INTO `project` (`id`, `title`, `description`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
        (1, 'html', 'chapitre 1', 'active', '2026-02-02', '2025-12-11', '2026-02-02 09:53:13', NULL),
        (2, 'java', 'chapitre 3', 'active', '2026-02-12', '2025-12-11', '2026-02-02 09:53:58', '2026-02-02 10:25:13'),
        (3, 'web', '4', 'draft', '2026-02-01', '2025-12-11', '2026-02-02 11:27:53', NULL),
        (4, 'base de donnes', 'chapitre 7', 'active', '2026-02-01', '2025-12-11', '2026-02-02 18:06:02', NULL),
        (5, 'python', 'c', 'draft', '2026-02-02', NULL, '2026-02-02 18:07:07', NULL),
        (6, 'javascript', 'chapitre6.7.8.9', 'draft', '2026-02-02', NULL, '2026-02-02 18:07:50', '2026-02-02 18:16:44'),
        (8, 'pi dev', 'chapitres1,2,3,4', 'completed', '2026-02-04', '2026-02-05', '2026-02-04 00:47:46', NULL),
        (9, 'math base1', 'chapitres4,6,7', 'draft', '2026-02-04', NULL, '2026-02-04 01:10:17', NULL),
        (10, 'physique', 'chapitres789456', 'draft', '2026-02-04', NULL, '2026-02-04 01:11:08', NULL),
        (11, 'svt', 'azert789456', 'draft', '2026-02-04', NULL, '2026-02-04 01:12:00', NULL);");

        // Depots
        $this->addSql("INSERT INTO `depots` (`id`, `title`, `description`, `type`, `file_path`, `file_size`, `file_type`, `uploaded_at`, `project_id`, `student_name`, `download_count`) VALUES
        (2, 'projetweb', 'rayensboui', 'document', 'k-698364ffc4e6c.pdf', '67861', 'application/pdf', '2026-02-04 16:25:51', 1, 'Étudiant InnoLearn', 0),
        (3, 'projetjava', 'qqqqqqqqqqqqqqqqqq', 'document', 'k-6983659d89ece.pdf', '67861', 'application/pdf', '2026-02-04 16:28:29', 2, 'Étudiant InnoLearn', 0),
        (5, 'projetjava', 'qqqqqqqqqqqqqqqq', 'document', 'k-698377c75f69f.pdf', '67861', 'application/pdf', '2026-02-04 17:45:59', 5, 'Étudiant InnoLearn', 0),
        (6, 'projetweb', 'gggggggggggggg', 'document', 'k-69838677c729a.pdf', '67861', 'application/pdf', '2026-02-04 18:48:39', 1, 'Étudiant InnoLearn', 0);");

        // Formulaires
        $this->addSql("INSERT INTO `formulaire` (`id`, `titre`, `description`, `temps_limite`, `category`) VALUES
        (2, 'test', 'lola is a good quiz you know?', 15, 'linuxx'),
        (3, 'matha', 'mecanique math test', 40, 'student'),
        (5, 'mini quiz', 'mini quiz to test', 7, 'java'),
        (7, 'sabaoui', 'saboui is a bad kid', 3, 'female'),
        (9, 'bomba', 'bomba bomba bomba', 3, 'bomba');");

        // Questions
        $this->addSql("INSERT INTO `question` (`id`, `formulaire_id`, `question_text`, `type`, `correct_answer`, `points`) VALUES
        (3, 2, 'how old are you?', 'number', '10', 3),
        (4, 2, 'yes or no', 'true_false', 'false', 2),
        (5, 2, 'test2', 'number', '5', 2),
        (6, 3, '5+5\\r\\n4\\r\\n10\\r\\n20', 'multiple_choice', '10', 1),
        (7, 3, 'text', 'true_false', 'false', 3),
        (8, 7, 'idiot?', 'true_false', 'true', 10),
        (9, 9, 'bomba?', 'multiple_choice', 'bomba', 4);");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
