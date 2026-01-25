<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123191917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payments (id SERIAL NOT NULL, niveau_id INT DEFAULT NULL, etudiant_id INT NOT NULL, type_id INT NOT NULL, reference VARCHAR(50) NOT NULL, montant DOUBLE PRECISION NOT NULL, date_payment TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, au INT NOT NULL, numero VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B32B3E9C81 ON payments (niveau_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32DDEAB1A3 ON payments (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32C54C8C93 ON payments (type_id)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32C54C8C93 FOREIGN KEY (type_id) REFERENCES type_droits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32B3E9C81');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32DDEAB1A3');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32C54C8C93');
        $this->addSql('DROP TABLE payments');
    }
}
