<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260108084738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE status_etudiants (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE niveau_etudiants ADD status_etudiant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61DD930C452 FOREIGN KEY (status_etudiant_id) REFERENCES status_etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_70ADE61DD930C452 ON niveau_etudiants (status_etudiant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61DD930C452');
        $this->addSql('DROP TABLE status_etudiants');
        $this->addSql('DROP INDEX IDX_70ADE61DD930C452');
        $this->addSql('ALTER TABLE niveau_etudiants DROP status_etudiant_id');
    }
}
