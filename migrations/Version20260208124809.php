<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208124809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, niveau VARCHAR(255) NOT NULL, datepublication DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE depots (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(50) NOT NULL, file_path VARCHAR(255) NOT NULL, file_size VARCHAR(50) DEFAULT NULL, file_type VARCHAR(100) DEFAULT NULL, uploaded_at DATETIME NOT NULL, student_name VARCHAR(100) NOT NULL, download_count INT DEFAULT 0 NOT NULL, project_id INT NOT NULL, INDEX IDX_D99EA427166D1F9C (project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type_evenement VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, lieu VARCHAR(50) NOT NULL, capacite INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE formulaire (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, temps_limite INT DEFAULT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscrit_event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, date_inscrit DATETIME NOT NULL, status VARCHAR(20) DEFAULT \'En attente\' NOT NULL, event_id INT NOT NULL, INDEX IDX_13E341C071F7E88B (event_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offrestage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, entreprise VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, competences LONGTEXT NOT NULL, duree INT NOT NULL, date_publication DATETIME NOT NULL, statut VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, question_text VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, correct_answer VARCHAR(255) NOT NULL, points INT NOT NULL, formulaire_id INT NOT NULL, INDEX IDX_B6F7494E5053569B (formulaire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE depots ADD CONSTRAINT FK_D99EA427166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscrit_event ADD CONSTRAINT FK_13E341C071F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E5053569B FOREIGN KEY (formulaire_id) REFERENCES formulaire (id)');
        $this->addSql('DROP TABLE offre_stage');
        $this->addSql('ALTER TABLE book_categorie CHANGE status status VARCHAR(20) DEFAULT \'active\' NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY `FK_FDCA8C9CBCF5E72D`');
        $this->addSql('DROP INDEX IDX_FDCA8C9CBCF5E72D ON cours');
        $this->addSql('ALTER TABLE cours ADD slug VARCHAR(255) NOT NULL, ADD type_media VARCHAR(50) NOT NULL, ADD media_url VARCHAR(500) NOT NULL, ADD enseignant VARCHAR(255) NOT NULL, ADD categorie_cours_id INT NOT NULL, CHANGE niveau niveau VARCHAR(50) NOT NULL, CHANGE titre nom VARCHAR(255) NOT NULL, CHANGE categorie_id duree INT NOT NULL, CHANGE datepublication date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C464839DA FOREIGN KEY (categorie_cours_id) REFERENCES categorie_cours (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C464839DA ON cours (categorie_cours_id)');
        $this->addSql('ALTER TABLE project CHANGE end_date end_date DATE DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user ADD country_code VARCHAR(5) DEFAULT NULL, ADD phone_number VARCHAR(255) NOT NULL, DROP first_name, DROP last_name, CHANGE roles roles JSON NOT NULL, CHANGE email name VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offre_stage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, entreprise VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, lieu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, domaine VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, competences LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, duree INT NOT NULL, date_publication DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE depots DROP FOREIGN KEY FK_D99EA427166D1F9C');
        $this->addSql('ALTER TABLE inscrit_event DROP FOREIGN KEY FK_13E341C071F7E88B');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E5053569B');
        $this->addSql('DROP TABLE categorie_cours');
        $this->addSql('DROP TABLE depots');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE formulaire');
        $this->addSql('DROP TABLE inscrit_event');
        $this->addSql('DROP TABLE offrestage');
        $this->addSql('DROP TABLE question');
        $this->addSql('ALTER TABLE book_categorie CHANGE status status VARCHAR(20) DEFAULT \'\'\'active\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C464839DA');
        $this->addSql('DROP INDEX IDX_FDCA8C9C464839DA ON cours');
        $this->addSql('ALTER TABLE cours ADD titre VARCHAR(255) NOT NULL, ADD categorie_id INT NOT NULL, DROP nom, DROP slug, DROP type_media, DROP media_url, DROP duree, DROP enseignant, DROP categorie_cours_id, CHANGE niveau niveau VARCHAR(255) NOT NULL, CHANGE date_creation datepublication DATETIME NOT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT `FK_FDCA8C9CBCF5E72D` FOREIGN KEY (categorie_id) REFERENCES cours_categorie (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9CBCF5E72D ON cours (categorie_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE project CHANGE end_date end_date DATE DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(255) NOT NULL, DROP country_code, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE name email VARCHAR(180) NOT NULL, CHANGE phone_number first_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
