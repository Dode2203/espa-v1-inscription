<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260127051456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ecolages (id SERIAL NOT NULL, formations_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_ecolage TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FD93F2AA3BF5B0C2 ON ecolages (formations_id)');
        $this->addSql('ALTER TABLE ecolages ADD CONSTRAINT FK_FD93F2AA3BF5B0C2 FOREIGN KEY (formations_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ecolages DROP CONSTRAINT FK_FD93F2AA3BF5B0C2');
        $this->addSql('DROP TABLE ecolages');
    }
}
