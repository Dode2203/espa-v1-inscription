<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204090048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT fk_227c02eb75eb8397');
        $this->addSql('DROP INDEX idx_227c02eb75eb8397');
        $this->addSql('ALTER TABLE etudiants DROP propos_id');
        $this->addSql('ALTER TABLE propos ADD etudiant_id INT NOT NULL');
        $this->addSql('ALTER TABLE propos ADD date_insertion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE propos ADD telephone VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE propos ADD CONSTRAINT FK_252FC726DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_252FC726DDEAB1A3 ON propos (etudiant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE etudiants ADD propos_id INT NOT NULL');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT fk_227c02eb75eb8397 FOREIGN KEY (propos_id) REFERENCES propos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_227c02eb75eb8397 ON etudiants (propos_id)');
        $this->addSql('ALTER TABLE propos DROP CONSTRAINT FK_252FC726DDEAB1A3');
        $this->addSql('DROP INDEX IDX_252FC726DDEAB1A3');
        $this->addSql('ALTER TABLE propos DROP etudiant_id');
        $this->addSql('ALTER TABLE propos DROP date_insertion');
        $this->addSql('ALTER TABLE propos DROP telephone');
    }
}
