<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260126112718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE droits_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payements_ecolages_id_seq CASCADE');
        $this->addSql('CREATE TABLE payments (id SERIAL NOT NULL, niveau_id INT DEFAULT NULL, etudiant_id INT NOT NULL, type_id INT NOT NULL, utilisateur_id INT DEFAULT NULL, reference VARCHAR(50) NOT NULL, montant DOUBLE PRECISION NOT NULL, date_payment TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, annee INT NOT NULL, numero VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B32B3E9C81 ON payments (niveau_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32DDEAB1A3 ON payments (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32C54C8C93 ON payments (type_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32FB88E14F ON payments (utilisateur_id)');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32C54C8C93 FOREIGN KEY (type_id) REFERENCES type_droits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payements_ecolages DROP CONSTRAINT fk_a6d31440ddeab1a3');
        $this->addSql('ALTER TABLE payements_ecolages DROP CONSTRAINT fk_a6d31440fb88e14f');
        $this->addSql('ALTER TABLE droits DROP CONSTRAINT fk_7a9d4ce9756148c');
        $this->addSql('ALTER TABLE droits DROP CONSTRAINT fk_7a9d4cefb88e14f');
        $this->addSql('ALTER TABLE droits DROP CONSTRAINT fk_7a9d4ceddeab1a3');
        $this->addSql('DROP TABLE propositions');
        $this->addSql('DROP TABLE payements_ecolages');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE droits');
        $this->addSql('DROP TABLE formation');
        $this->addSql('ALTER TABLE bacc ALTER annee DROP NOT NULL');
        $this->addSql('ALTER TABLE bacc ALTER serie DROP NOT NULL');
        $this->addSql('ALTER TABLE cin ALTER date_cin DROP NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER nom DROP NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER prenom DROP NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER lieu_naissance DROP NOT NULL');
        $this->addSql('ALTER TABLE propos ALTER adresse DROP NOT NULL');
        $this->addSql('ALTER TABLE propos ALTER email DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE droits_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payements_ecolages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE propositions (id BIGINT DEFAULT NULL, nom TEXT DEFAULT NULL)');
        $this->addSql('CREATE TABLE payements_ecolages (id SERIAL NOT NULL, etudiant_id INT NOT NULL, utilisateur_id INT NOT NULL, reference VARCHAR(255) NOT NULL, datepayements TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, montant DOUBLE PRECISION NOT NULL, tranche INT NOT NULL, annee INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_a6d31440fb88e14f ON payements_ecolages (utilisateur_id)');
        $this->addSql('CREATE INDEX idx_a6d31440ddeab1a3 ON payements_ecolages (etudiant_id)');
        $this->addSql('CREATE TABLE niveau (nom TEXT DEFAULT NULL)');
        $this->addSql('CREATE TABLE droits (id SERIAL NOT NULL, type_droit_id INT NOT NULL, utilisateur_id INT NOT NULL, etudiant_id INT NOT NULL, reference VARCHAR(255) NOT NULL, date_versement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, montant DOUBLE PRECISION NOT NULL, annee INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7a9d4ceddeab1a3 ON droits (etudiant_id)');
        $this->addSql('CREATE INDEX idx_7a9d4cefb88e14f ON droits (utilisateur_id)');
        $this->addSql('CREATE INDEX idx_7a9d4ce9756148c ON droits (type_droit_id)');
        $this->addSql('CREATE TABLE formation (nom TEXT DEFAULT NULL)');
        $this->addSql('ALTER TABLE payements_ecolages ADD CONSTRAINT fk_a6d31440ddeab1a3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payements_ecolages ADD CONSTRAINT fk_a6d31440fb88e14f FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE droits ADD CONSTRAINT fk_7a9d4ce9756148c FOREIGN KEY (type_droit_id) REFERENCES type_droits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE droits ADD CONSTRAINT fk_7a9d4cefb88e14f FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE droits ADD CONSTRAINT fk_7a9d4ceddeab1a3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32B3E9C81');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32DDEAB1A3');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32C54C8C93');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32FB88E14F');
        $this->addSql('DROP TABLE payments');
        $this->addSql('ALTER TABLE cin ALTER date_cin SET NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER nom SET NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER prenom SET NOT NULL');
        $this->addSql('ALTER TABLE etudiants ALTER lieu_naissance SET NOT NULL');
        $this->addSql('ALTER TABLE propos ALTER adresse SET NOT NULL');
        $this->addSql('ALTER TABLE propos ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE bacc ALTER annee SET NOT NULL');
        $this->addSql('ALTER TABLE bacc ALTER serie SET NOT NULL');
    }
}
