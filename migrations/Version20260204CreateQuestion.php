<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260204CreateQuestion extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table question and link it to formulaire';
    }

    public function up(Schema $schema): void
    {
        // Create question table
        $this->addSql('CREATE TABLE question (
            id INT AUTO_INCREMENT NOT NULL,
            formulaire_id INT NOT NULL,
            question_text VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            correct_answer VARCHAR(255) NOT NULL,
            points INT NOT NULL,
            INDEX IDX_FORMULAIRE (formulaire_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key to formulaire
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_FORMULAIRE FOREIGN KEY (formulaire_id) REFERENCES formulaire (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop question table if rolling back
        $this->addSql('DROP TABLE question');
    }
}
