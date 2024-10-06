<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241006144939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP CONSTRAINT fk_729f519b88688bb9');
        $this->addSql('DROP INDEX idx_729f519b88688bb9');
        $this->addSql('ALTER TABLE room DROP bed_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room ADD bed_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT fk_729f519b88688bb9 FOREIGN KEY (bed_id) REFERENCES bed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_729f519b88688bb9 ON room (bed_id)');
    }
}
