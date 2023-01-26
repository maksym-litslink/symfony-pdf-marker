<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126205521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE test_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE test (id INT NOT NULL, pdf_id INT DEFAULT NULL, start_offset INT NOT NULL, end_offset INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D87F7E0C511FC912 ON test (pdf_id)');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C511FC912 FOREIGN KEY (pdf_id) REFERENCES pdf (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE test_id_seq CASCADE');
        $this->addSql('ALTER TABLE test DROP CONSTRAINT FK_D87F7E0C511FC912');
        $this->addSql('DROP TABLE test');
    }
}
