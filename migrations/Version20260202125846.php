<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202125846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nationalites (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, type INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE etudiants ADD nationalite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EB1B063272 FOREIGN KEY (nationalite_id) REFERENCES nationalites (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_227C02EB1B063272 ON etudiants (nationalite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT FK_227C02EB1B063272');
        $this->addSql('DROP TABLE nationalites');
        $this->addSql('DROP INDEX IDX_227C02EB1B063272');
        $this->addSql('ALTER TABLE etudiants DROP nationalite_id');
    }
}
