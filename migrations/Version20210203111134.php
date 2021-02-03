<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210203111134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chain_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chain_configuration (id INT NOT NULL, chain_main_name VARCHAR(255) NOT NULL, start_value VARCHAR(255) DEFAULT \'1\' NOT NULL, increment INT DEFAULT 1 NOT NULL, direction INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');

        $this->addSql('ALTER TABLE unique_identifiers ADD chain_configuration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE unique_identifiers ADD CONSTRAINT FK_CDB3BE5D87CC7349 FOREIGN KEY (chain_configuration_id) REFERENCES chain_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDB3BE5D87CC7349 ON unique_identifiers (chain_configuration_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE unique_identifiers DROP CONSTRAINT FK_CDB3BE5D87CC7349');
        $this->addSql('DROP SEQUENCE chain_configuration_id_seq CASCADE');
        $this->addSql('DROP TABLE chain_configuration');
        $this->addSql('DROP INDEX UNIQ_CDB3BE5D87CC7349');
        $this->addSql('ALTER TABLE unique_identifiers DROP chain_configuration_id');
    }
}
