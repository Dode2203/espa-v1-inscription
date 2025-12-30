<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229105836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE droits ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE droits ADD CONSTRAINT FK_7A9D4CEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7A9D4CEFB88E14F ON droits (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE droits DROP CONSTRAINT FK_7A9D4CEFB88E14F');
        $this->addSql('DROP INDEX IDX_7A9D4CEFB88E14F');
        $this->addSql('ALTER TABLE droits DROP utilisateur_id');
    }
}
