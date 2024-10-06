<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241006174329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bed ADD is_available BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE bed ADD bed_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD capacity INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD description TEXT NOT NULL');
        $this->addSql('ALTER TABLE room ADD is_available BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bed DROP is_available');
        $this->addSql('ALTER TABLE bed DROP bed_number');
        $this->addSql('ALTER TABLE room DROP capacity');
        $this->addSql('ALTER TABLE room DROP type');
        $this->addSql('ALTER TABLE room DROP description');
        $this->addSql('ALTER TABLE room DROP is_available');
    }
}
