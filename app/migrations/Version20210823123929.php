<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210823123929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tokens (id INT NOT NULL, user_id INT NOT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AA5A118EA76ED395 ON tokens (user_id)');
        $this->addSql('COMMENT ON COLUMN tokens.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP api_token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tokens_id_seq CASCADE');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('ALTER TABLE "user" ADD api_token VARCHAR(255) DEFAULT NULL');
    }
}
