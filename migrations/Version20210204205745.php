<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204205745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('DROP INDEX carriage_uniq_index');
        $this->addSql('DROP INDEX left_right_uniq_idx');
        $this->addSql('CREATE UNIQUE INDEX brand_slug_idx ON chain_data (unique_identifiers_id, chain_data_name)');
        $this->addSql('CREATE UNIQUE INDEX carriage_uniq_index ON chain_data (unique_identifiers_id, carriage) WHERE (carriage != \'f\')');
        $this->addSql('CREATE UNIQUE INDEX left_right_uniq_idx ON chain_data (right_id, left_id, unique_identifiers_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX brand_slug_idx');
        $this->addSql('DROP INDEX left_right_uniq_idx');
        $this->addSql('DROP INDEX carriage_uniq_index');
        $this->addSql('CREATE UNIQUE INDEX left_right_uniq_idx ON chain_data (right_id, left_id)');
        $this->addSql('CREATE UNIQUE INDEX carriage_uniq_index ON chain_data (unique_identifiers_id, carriage) WHERE (carriage <> false)');
    }
}
