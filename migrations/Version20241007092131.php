<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007092131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, number_of_people INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE booking_room (booking_id INT NOT NULL, room_id INT NOT NULL, PRIMARY KEY(booking_id, room_id))');
        $this->addSql('CREATE INDEX IDX_6A0E73D53301C60 ON booking_room (booking_id)');
        $this->addSql('CREATE INDEX IDX_6A0E73D554177093 ON booking_room (room_id)');
        $this->addSql('ALTER TABLE booking_room ADD CONSTRAINT FK_6A0E73D53301C60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking_room ADD CONSTRAINT FK_6A0E73D554177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('ALTER TABLE booking_room DROP CONSTRAINT FK_6A0E73D53301C60');
        $this->addSql('ALTER TABLE booking_room DROP CONSTRAINT FK_6A0E73D554177093');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE booking_room');
    }
}
