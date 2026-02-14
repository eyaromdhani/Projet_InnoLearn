<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208125440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, publier DATE NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE book_categorie (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, status VARCHAR(20) DEFAULT \'active\' NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categorie_cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, niveau VARCHAR(255) NOT NULL, datepublication DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, type_media VARCHAR(50) NOT NULL, media_url VARCHAR(500) NOT NULL, duree INT NOT NULL, niveau VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL, enseignant VARCHAR(255) NOT NULL, categorie_cours_id INT NOT NULL, INDEX IDX_FDCA8C9C464839DA (categorie_cours_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cours_categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, datecreation DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE depots (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(50) NOT NULL, file_path VARCHAR(255) NOT NULL, file_size VARCHAR(50) DEFAULT NULL, file_type VARCHAR(100) DEFAULT NULL, uploaded_at DATETIME NOT NULL, student_name VARCHAR(100) NOT NULL, download_count INT DEFAULT 0 NOT NULL, project_id INT NOT NULL, INDEX IDX_D99EA427166D1F9C (project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type_evenement VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, lieu VARCHAR(50) NOT NULL, capacite INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE formulaire (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, temps_limite INT DEFAULT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscrit_event (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, date_inscrit DATETIME NOT NULL, status VARCHAR(20) DEFAULT \'En attente\' NOT NULL, event_id INT NOT NULL, INDEX IDX_13E341C071F7E88B (event_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offrestage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, entreprise VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, competences LONGTEXT NOT NULL, duree INT NOT NULL, date_publication DATETIME NOT NULL, statut VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(50) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, question_text VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, correct_answer VARCHAR(255) NOT NULL, points INT NOT NULL, formulaire_id INT NOT NULL, INDEX IDX_B6F7494E5053569B (formulaire_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, duration INT NOT NULL, difficulty VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stagecondidature (id INT AUTO_INCREMENT NOT NULL, type_request VARCHAR(255) NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, domaine VARCHAR(255) NOT NULL, competences LONGTEXT NOT NULL, cv VARCHAR(255) NOT NULL, lettre_motivation LONGTEXT NOT NULL, date_publication DATE NOT NULL, statut VARCHAR(255) NOT NULL, id_etudiant INT NOT NULL, id_offre INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, country_code VARCHAR(5) DEFAULT NULL, phone_number VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C464839DA FOREIGN KEY (categorie_cours_id) REFERENCES categorie_cours (id)');
        $this->addSql('ALTER TABLE depots ADD CONSTRAINT FK_D99EA427166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscrit_event ADD CONSTRAINT FK_13E341C071F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E5053569B FOREIGN KEY (formulaire_id) REFERENCES formulaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C464839DA');
        $this->addSql('ALTER TABLE depots DROP FOREIGN KEY FK_D99EA427166D1F9C');
        $this->addSql('ALTER TABLE inscrit_event DROP FOREIGN KEY FK_13E341C071F7E88B');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E5053569B');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_categorie');
        $this->addSql('DROP TABLE categorie_cours');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE cours_categorie');
        $this->addSql('DROP TABLE depots');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE formulaire');
        $this->addSql('DROP TABLE inscrit_event');
        $this->addSql('DROP TABLE offrestage');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE stagecondidature');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
