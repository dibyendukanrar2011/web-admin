<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305091907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE web_user ADD dob DATE DEFAULT NULL, ADD gender VARCHAR(255) DEFAULT NULL, ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD address LONGTEXT DEFAULT NULL, CHANGE status status ENUM(\'Active\', \'Inactive\', \'Deleted\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `web_user` DROP dob, DROP gender, DROP profile_picture, DROP address, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
