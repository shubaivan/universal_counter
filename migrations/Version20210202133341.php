<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210202133341 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chain_data ADD left_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chain_data ADD right_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chain_data ADD CONSTRAINT FK_388447E5E26CCE02 FOREIGN KEY (left_id) REFERENCES chain_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chain_data ADD CONSTRAINT FK_388447E554976835 FOREIGN KEY (right_id) REFERENCES chain_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX left_right_uniq_idx ON chain_data (right_id, left_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chain_data DROP CONSTRAINT FK_388447E5E26CCE02');
        $this->addSql('ALTER TABLE chain_data DROP CONSTRAINT FK_388447E554976835');
        $this->addSql('DROP INDEX left_right_uniq_idx');
        $this->addSql('ALTER TABLE chain_data DROP left_id');
        $this->addSql('ALTER TABLE chain_data DROP right_id');
    }
}
