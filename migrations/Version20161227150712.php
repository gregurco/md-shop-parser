<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161227150712 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE online_price online_price NUMERIC(10, 2) DEFAULT NULL COMMENT \'New price in online shop\', CHANGE special_price special_price NUMERIC(10, 2) DEFAULT NULL COMMENT \'New price in retail shop\', CHANGE old_price old_price NUMERIC(10, 2) DEFAULT NULL COMMENT \'Old price\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE online_price online_price NUMERIC(10, 2) DEFAULT NULL, CHANGE special_price special_price NUMERIC(10, 2) DEFAULT NULL, CHANGE old_price old_price NUMERIC(10, 2) DEFAULT NULL');
    }
}
