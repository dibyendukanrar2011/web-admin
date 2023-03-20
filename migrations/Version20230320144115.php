<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320144115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_user CHANGE status status ENUM(\'Active\', \'Inactive\', \'Deleted\')');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4991DBBCAAA18E60 ON web_user (cellphone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_4991DBBCAAA18E60 ON `web_user`');
        $this->addSql('ALTER TABLE `web_user` CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
