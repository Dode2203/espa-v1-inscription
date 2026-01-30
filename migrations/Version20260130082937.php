<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130082937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bacc (id SERIAL NOT NULL, numero VARCHAR(255) DEFAULT NULL, annee INT DEFAULT NULL, serie VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE cin (id SERIAL NOT NULL, numero VARCHAR(50) NOT NULL, date_cin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, lieu VARCHAR(255) NOT NULL, ancien_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nouveau_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ecolages (id SERIAL NOT NULL, formations_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_ecolage TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD93F2AA3BF5B0C2 ON ecolages (formations_id)');
        $this->addSql('CREATE TABLE etudiants (id SERIAL NOT NULL, cin_id INT DEFAULT NULL, bacc_id INT NOT NULL, propos_id INT NOT NULL, sexe_id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, date_naissance TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, lieu_naissance VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_227C02EBE9795579 ON etudiants (cin_id)');
        $this->addSql('CREATE INDEX IDX_227C02EB2CEC171F ON etudiants (bacc_id)');
        $this->addSql('CREATE INDEX IDX_227C02EB75EB8397 ON etudiants (propos_id)');
        $this->addSql('CREATE INDEX IDX_227C02EB448F3B3C ON etudiants (sexe_id)');
        $this->addSql('CREATE TABLE formation_etudiants (id SERIAL NOT NULL, etudiant_id INT NOT NULL, formation_id INT NOT NULL, date_formation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E8015236DDEAB1A3 ON formation_etudiants (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_E80152365200282E ON formation_etudiants (formation_id)');
        $this->addSql('CREATE TABLE formations (id SERIAL NOT NULL, type_formation_id INT NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_40902137D543922B ON formations (type_formation_id)');
        $this->addSql('CREATE TABLE inscrits (id SERIAL NOT NULL, utilisateur_id INT NOT NULL, etudiant_id INT NOT NULL, description VARCHAR(255) NOT NULL, date_inscription TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, matricule VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2644257FFB88E14F ON inscrits (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_2644257FDDEAB1A3 ON inscrits (etudiant_id)');
        $this->addSql('CREATE TABLE mentions (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, abr VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE niveau_etudiants (id SERIAL NOT NULL, niveau_id INT DEFAULT NULL, mention_id INT NOT NULL, etudiant_id INT NOT NULL, status_etudiant_id INT DEFAULT NULL, annee INT NOT NULL, date_insertion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_70ADE61DB3E9C81 ON niveau_etudiants (niveau_id)');
        $this->addSql('CREATE INDEX IDX_70ADE61D7A4147F0 ON niveau_etudiants (mention_id)');
        $this->addSql('CREATE INDEX IDX_70ADE61DDDEAB1A3 ON niveau_etudiants (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_70ADE61DD930C452 ON niveau_etudiants (status_etudiant_id)');
        $this->addSql('CREATE TABLE niveaux (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, type SMALLINT NOT NULL, grade SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE parcours (id SERIAL NOT NULL, mention_id INT NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99B1DEE37A4147F0 ON parcours (mention_id)');
        $this->addSql('CREATE TABLE payments (id SERIAL NOT NULL, niveau_id INT DEFAULT NULL, etudiant_id INT NOT NULL, type_id INT NOT NULL, utilisateur_id INT DEFAULT NULL, reference VARCHAR(50) NOT NULL, montant DOUBLE PRECISION NOT NULL, date_payment TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, annee INT NOT NULL, numero VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B32B3E9C81 ON payments (niveau_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32DDEAB1A3 ON payments (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32C54C8C93 ON payments (type_id)');
        $this->addSql('CREATE INDEX IDX_65D29B32FB88E14F ON payments (utilisateur_id)');
        $this->addSql('CREATE TABLE propos (id SERIAL NOT NULL, adresse VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sexes (id SERIAL NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE status (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE status_etudiants (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_droits (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_formations (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE utilisateur (id SERIAL NOT NULL, role_id INT NOT NULL, status_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1D1C63B3D60322AC ON utilisateur (role_id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B36BF700BD ON utilisateur (status_id)');
        $this->addSql('ALTER TABLE ecolages ADD CONSTRAINT FK_FD93F2AA3BF5B0C2 FOREIGN KEY (formations_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EBE9795579 FOREIGN KEY (cin_id) REFERENCES cin (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EB2CEC171F FOREIGN KEY (bacc_id) REFERENCES bacc (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EB75EB8397 FOREIGN KEY (propos_id) REFERENCES propos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etudiants ADD CONSTRAINT FK_227C02EB448F3B3C FOREIGN KEY (sexe_id) REFERENCES sexes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_etudiants ADD CONSTRAINT FK_E8015236DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_etudiants ADD CONSTRAINT FK_E80152365200282E FOREIGN KEY (formation_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formations ADD CONSTRAINT FK_40902137D543922B FOREIGN KEY (type_formation_id) REFERENCES type_formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inscrits ADD CONSTRAINT FK_2644257FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inscrits ADD CONSTRAINT FK_2644257FDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61DB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61D7A4147F0 FOREIGN KEY (mention_id) REFERENCES mentions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61DDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE niveau_etudiants ADD CONSTRAINT FK_70ADE61DD930C452 FOREIGN KEY (status_etudiant_id) REFERENCES status_etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parcours ADD CONSTRAINT FK_99B1DEE37A4147F0 FOREIGN KEY (mention_id) REFERENCES mentions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32C54C8C93 FOREIGN KEY (type_id) REFERENCES type_droits (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B32FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B36BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecolages DROP CONSTRAINT FK_FD93F2AA3BF5B0C2');
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT FK_227C02EBE9795579');
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT FK_227C02EB2CEC171F');
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT FK_227C02EB75EB8397');
        $this->addSql('ALTER TABLE etudiants DROP CONSTRAINT FK_227C02EB448F3B3C');
        $this->addSql('ALTER TABLE formation_etudiants DROP CONSTRAINT FK_E8015236DDEAB1A3');
        $this->addSql('ALTER TABLE formation_etudiants DROP CONSTRAINT FK_E80152365200282E');
        $this->addSql('ALTER TABLE formations DROP CONSTRAINT FK_40902137D543922B');
        $this->addSql('ALTER TABLE inscrits DROP CONSTRAINT FK_2644257FFB88E14F');
        $this->addSql('ALTER TABLE inscrits DROP CONSTRAINT FK_2644257FDDEAB1A3');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61DB3E9C81');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61D7A4147F0');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61DDDEAB1A3');
        $this->addSql('ALTER TABLE niveau_etudiants DROP CONSTRAINT FK_70ADE61DD930C452');
        $this->addSql('ALTER TABLE parcours DROP CONSTRAINT FK_99B1DEE37A4147F0');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32B3E9C81');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32DDEAB1A3');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32C54C8C93');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32FB88E14F');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B3D60322AC');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B36BF700BD');
        $this->addSql('DROP TABLE bacc');
        $this->addSql('DROP TABLE cin');
        $this->addSql('DROP TABLE ecolages');
        $this->addSql('DROP TABLE etudiants');
        $this->addSql('DROP TABLE formation_etudiants');
        $this->addSql('DROP TABLE formations');
        $this->addSql('DROP TABLE inscrits');
        $this->addSql('DROP TABLE mentions');
        $this->addSql('DROP TABLE niveau_etudiants');
        $this->addSql('DROP TABLE niveaux');
        $this->addSql('DROP TABLE parcours');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE propos');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE sexes');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE status_etudiants');
        $this->addSql('DROP TABLE type_droits');
        $this->addSql('DROP TABLE type_formations');
        $this->addSql('DROP TABLE utilisateur');
    }
}
