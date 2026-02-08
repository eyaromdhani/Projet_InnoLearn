<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260202CreateFormulaire extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create formulaire table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE formulaire (
            id INT AUTO_INCREMENT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description VARCHAR(255) NOT NULL,
            temps_limite INT DEFAULT NULL,
            category VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE formulaire');
    }
}
