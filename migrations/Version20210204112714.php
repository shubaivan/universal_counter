<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204112714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chain_data ALTER left_id DROP NOT NULL');
        $this->addSql('ALTER TABLE chain_data ALTER carriage SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_388447E514522991 ON chain_data (chain_data_name)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('DROP INDEX UNIQ_388447E514522991');
        $this->addSql('ALTER TABLE chain_data ALTER left_id SET NOT NULL');
        $this->addSql('ALTER TABLE chain_data ALTER carriage DROP NOT NULL');
    }
}
