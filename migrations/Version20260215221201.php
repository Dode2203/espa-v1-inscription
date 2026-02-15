<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215221201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE preinscription (id SERIAL NOT NULL, mention_id INT NOT NULL, formation_id INT NOT NULL, niveau_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, converted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_649A5F6F7A4147F0 ON preinscription (mention_id)');
        $this->addSql('CREATE INDEX IDX_649A5F6F5200282E ON preinscription (formation_id)');
        $this->addSql('CREATE INDEX IDX_649A5F6FB3E9C81 ON preinscription (niveau_id)');
        $this->addSql('ALTER TABLE preinscription ADD CONSTRAINT FK_649A5F6F7A4147F0 FOREIGN KEY (mention_id) REFERENCES mentions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preinscription ADD CONSTRAINT FK_649A5F6F5200282E FOREIGN KEY (formation_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preinscription ADD CONSTRAINT FK_649A5F6FB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveaux (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE preinscription DROP CONSTRAINT FK_649A5F6F7A4147F0');
        $this->addSql('ALTER TABLE preinscription DROP CONSTRAINT FK_649A5F6F5200282E');
        $this->addSql('ALTER TABLE preinscription DROP CONSTRAINT FK_649A5F6FB3E9C81');
        $this->addSql('DROP TABLE preinscription');
    }
}
