<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205194850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type_evenement VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, lieu VARCHAR(50) NOT NULL, capacite INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE offre_stage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, entreprise VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, competences LONGTEXT NOT NULL, duree INT NOT NULL, date_publication DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP TABLE offrestage');
        $this->addSql('ALTER TABLE book_categorie CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE quiz CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE stagecondidature CHANGE type_request type_request VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE competences competences LONGTEXT NOT NULL, CHANGE lettre_motivation lettre_motivation LONGTEXT NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offrestage (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, entreprise VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, lieu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, domaine VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, competences TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, duree INT NOT NULL, date_publication DATE NOT NULL, statut ENUM(\'ouverte\', \'fermée\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, id_recruteur INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE offre_stage');
        $this->addSql('ALTER TABLE book_categorie MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE book_categorie CHANGE id id INT NOT NULL, DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE quiz CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stagecondidature CHANGE type_request type_request ENUM(\'offre\', \'demande\') NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE competences competences TEXT NOT NULL, CHANGE lettre_motivation lettre_motivation TEXT NOT NULL, CHANGE statut statut ENUM(\'en_attente\', \'selectionnee\', \'refusee\', \'contactee\') NOT NULL');
    }
}
