<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202111638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE unique_identifiers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE unique_identifiers (id INT NOT NULL, request_hash UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN unique_identifiers.request_hash IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE unique_identifiers_id_seq CASCADE');
        $this->addSql('DROP TABLE unique_identifiers');
    }
}
