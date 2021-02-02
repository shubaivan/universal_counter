<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202131653 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chain_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chain_data (id INT NOT NULL, unique_identifiers_id INT DEFAULT NULL, chain_data_name VARCHAR(255) NOT NULL, carriage BOOLEAN DEFAULT \'false\', PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_388447E52A0B191E ON chain_data (unique_identifiers_id)');
        $this->addSql('ALTER TABLE chain_data ADD CONSTRAINT FK_388447E52A0B191E FOREIGN KEY (unique_identifiers_id) REFERENCES unique_identifiers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chain_data_id_seq CASCADE');
        $this->addSql('DROP TABLE chain_data');
    }
}
