<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202125123 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE request_approach ADD unique_identifiers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE request_approach ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE request_approach ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE request_approach ADD CONSTRAINT FK_CFF766602A0B191E FOREIGN KEY (unique_identifiers_id) REFERENCES unique_identifiers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFF766602A0B191E ON request_approach (unique_identifiers_id)');
        $this->addSql('ALTER TABLE unique_identifiers ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE unique_identifiers ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX request_hash_uniq_idx ON unique_identifiers (request_hash)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE request_approach DROP CONSTRAINT FK_CFF766602A0B191E');
        $this->addSql('DROP INDEX UNIQ_CFF766602A0B191E');
        $this->addSql('ALTER TABLE request_approach DROP unique_identifiers_id');
        $this->addSql('ALTER TABLE request_approach DROP created_at');
        $this->addSql('ALTER TABLE request_approach DROP updated_at');
        $this->addSql('DROP INDEX request_hash_uniq_idx');
        $this->addSql('ALTER TABLE unique_identifiers DROP created_at');
        $this->addSql('ALTER TABLE unique_identifiers DROP updated_at');
    }
}
