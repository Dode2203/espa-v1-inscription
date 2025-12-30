<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229111528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE droits ADD etudiant_id INT NOT NULL');
        $this->addSql('ALTER TABLE droits ADD CONSTRAINT FK_7A9D4CEDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7A9D4CEDDEAB1A3 ON droits (etudiant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE droits DROP CONSTRAINT FK_7A9D4CEDDEAB1A3');
        $this->addSql('DROP INDEX IDX_7A9D4CEDDEAB1A3');
        $this->addSql('ALTER TABLE droits DROP etudiant_id');
    }
}
