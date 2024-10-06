<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241006154926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bed ADD room_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bed ADD number INT NOT NULL');
        $this->addSql('ALTER TABLE bed DROP bed_number');
        $this->addSql('ALTER TABLE bed DROP availability_status');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT FK_E647FCFF54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E647FCFF54177093 ON bed (room_id)');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT fk_729f519b88688bb9');
        $this->addSql('DROP INDEX idx_729f519b88688bb9');
        $this->addSql('ALTER TABLE room ADD name TEXT NOT NULL');
        $this->addSql('ALTER TABLE room DROP bed_id');
        $this->addSql('ALTER TABLE room DROP room_number');
        $this->addSql('ALTER TABLE room DROP capacity');
        $this->addSql('ALTER TABLE room DROP room_type');
        $this->addSql('ALTER TABLE room DROP description');
        $this->addSql('ALTER TABLE room DROP availability_status');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room ADD bed_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD room_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD capacity INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD room_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD availability_status BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE room DROP name');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT fk_729f519b88688bb9 FOREIGN KEY (bed_id) REFERENCES bed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_729f519b88688bb9 ON room (bed_id)');
        $this->addSql('ALTER TABLE bed DROP CONSTRAINT FK_E647FCFF54177093');
        $this->addSql('DROP INDEX IDX_E647FCFF54177093');
        $this->addSql('ALTER TABLE bed ADD bed_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bed ADD availability_status BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE bed DROP room_id');
        $this->addSql('ALTER TABLE bed DROP number');
    }
}
