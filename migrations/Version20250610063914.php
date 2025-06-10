<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610063914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_64C19C15E237E06 ON category (name)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story CHANGE is_finish is_finish INT NOT NULL, CHANGE cover cover VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL, ADD create_account DATETIME NOT NULL, ADD delete_account DATETIME DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_64C19C15E237E06 ON category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story CHANGE is_finish is_finish TINYINT(1) NOT NULL, CHANGE cover cover VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP is_verified, DROP create_account, DROP delete_account, CHANGE password password VARCHAR(255) NOT NULL
        SQL);
    }
}
