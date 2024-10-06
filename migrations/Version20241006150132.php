<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241006150132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bed DROP CONSTRAINT fk_e647fcff54177093');
        $this->addSql('DROP INDEX idx_e647fcff54177093');
        $this->addSql('ALTER TABLE bed DROP room_id');
        $this->addSql('ALTER TABLE room ADD bed_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B88688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_729F519B88688BB9 ON room (bed_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B88688BB9');
        $this->addSql('DROP INDEX IDX_729F519B88688BB9');
        $this->addSql('ALTER TABLE room DROP bed_id');
        $this->addSql('ALTER TABLE bed ADD room_id INT NOT NULL');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT fk_e647fcff54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e647fcff54177093 ON bed (room_id)');
    }
}
