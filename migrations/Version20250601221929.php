<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601221929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE story_category (story_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_5A9075DAA5D4036 (story_id), INDEX IDX_5A9075D12469DE2 (category_id), PRIMARY KEY(story_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_category ADD CONSTRAINT FK_5A9075DAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_category ADD CONSTRAINT FK_5A9075D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_story DROP FOREIGN KEY FK_3654B7112469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_story DROP FOREIGN KEY FK_3654B71AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_story
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category_story (category_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_3654B7112469DE2 (category_id), INDEX IDX_3654B71AA5D4036 (story_id), PRIMARY KEY(category_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_story ADD CONSTRAINT FK_3654B7112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_story ADD CONSTRAINT FK_3654B71AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_category DROP FOREIGN KEY FK_5A9075DAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_category DROP FOREIGN KEY FK_5A9075D12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE story_category
        SQL);
    }
}
