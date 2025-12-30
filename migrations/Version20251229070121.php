<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229070121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_etudiants RENAME COLUMN dateformation TO date_formation');
        $this->addSql('ALTER TABLE niveau_etudiants ADD etudiant_id INT NOT NULL');
        $this->addSql('ALTER TABLE niveau_etudiants RENAME COLUMN dateinsertion TO date_insertion');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61DDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_70ADE61DDDEAB1A3 ON niveau_etudiants (etudiant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61DDDEAB1A3');
        $this->addSql('DROP INDEX IDX_70ADE61DDDEAB1A3');
        $this->addSql('ALTER TABLE niveau_etudiants DROP etudiant_id');
        $this->addSql('ALTER TABLE niveau_etudiants RENAME COLUMN date_insertion TO dateinsertion');
        $this->addSql('ALTER TABLE formation_etudiants RENAME COLUMN date_formation TO dateformation');
    }
}
