<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241011153024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE bed_booking_period_id_seq CASCADE');
        $this->addSql('ALTER TABLE booking_bed DROP CONSTRAINT fk_c022c5283301c60');
        $this->addSql('ALTER TABLE booking_bed DROP CONSTRAINT fk_c022c52888688bb9');
        $this->addSql('ALTER TABLE bed_booking_period DROP CONSTRAINT fk_9ab76d4488688bb9');
        $this->addSql('DROP TABLE booking_bed');
        $this->addSql('DROP TABLE bed_booking_period');
        $this->addSql('ALTER TABLE booking ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE booking ADD phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE booking ADD number_of_people INT NOT NULL');
        $this->addSql('ALTER TABLE booking DROP total_amount');
        $this->addSql('ALTER TABLE booking DROP status');
        $this->addSql('ALTER TABLE booking DROP first_name');
        $this->addSql('ALTER TABLE booking DROP last_name');
        $this->addSql('ALTER TABLE booking DROP phone_number');
        $this->addSql('ALTER TABLE booking ALTER start_date TYPE DATE');
        $this->addSql('ALTER TABLE booking ALTER end_date TYPE DATE');
        $this->addSql('ALTER TABLE booking ALTER email TYPE VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE bed_booking_period_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking_bed (booking_id INT NOT NULL, bed_id INT NOT NULL, PRIMARY KEY(booking_id, bed_id))');
        $this->addSql('CREATE INDEX idx_c022c52888688bb9 ON booking_bed (bed_id)');
        $this->addSql('CREATE INDEX idx_c022c5283301c60 ON booking_bed (booking_id)');
        $this->addSql('CREATE TABLE bed_booking_period (id INT NOT NULL, bed_id INT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9ab76d4488688bb9 ON bed_booking_period (bed_id)');
        $this->addSql('ALTER TABLE booking_bed ADD CONSTRAINT fk_c022c5283301c60 FOREIGN KEY (booking_id) REFERENCES booking (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking_bed ADD CONSTRAINT fk_c022c52888688bb9 FOREIGN KEY (bed_id) REFERENCES bed (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bed_booking_period ADD CONSTRAINT fk_9ab76d4488688bb9 FOREIGN KEY (bed_id) REFERENCES bed (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD total_amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE booking ADD status TEXT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD first_name TEXT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD last_name TEXT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD phone_number TEXT NOT NULL');
        $this->addSql('ALTER TABLE booking DROP name');
        $this->addSql('ALTER TABLE booking DROP phone');
        $this->addSql('ALTER TABLE booking DROP number_of_people');
        $this->addSql('ALTER TABLE booking ALTER start_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE booking ALTER end_date TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE booking ALTER email TYPE TEXT');
    }
}
