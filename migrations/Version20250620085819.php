<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620085819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE like_story (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_5C322DDFA76ED395 (user_id), INDEX IDX_5C322DDFAA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE follow_story (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_FEB2EE82A76ED395 (user_id), INDEX IDX_FEB2EE82AA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE like_chapter (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_AA0CFF0DA76ED395 (user_id), INDEX IDX_AA0CFF0D579F4768 (chapter_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE have_read (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_C9222AAEA76ED395 (user_id), INDEX IDX_C9222AAE579F4768 (chapter_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_story ADD CONSTRAINT FK_5C322DDFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_story ADD CONSTRAINT FK_5C322DDFAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follow_story ADD CONSTRAINT FK_FEB2EE82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follow_story ADD CONSTRAINT FK_FEB2EE82AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_chapter ADD CONSTRAINT FK_AA0CFF0DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_chapter ADD CONSTRAINT FK_AA0CFF0D579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE have_read ADD CONSTRAINT FK_C9222AAEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE have_read ADD CONSTRAINT FK_C9222AAE579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory DROP FOREIGN KEY FK_88B4877EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory DROP FOREIGN KEY FK_88B4877EAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveread DROP FOREIGN KEY FK_7E57ABAC579F4768
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveread DROP FOREIGN KEY FK_7E57ABACA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likechapter DROP FOREIGN KEY FK_3E6B415F579F4768
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likechapter DROP FOREIGN KEY FK_3E6B415FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory DROP FOREIGN KEY FK_6F06A85DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory DROP FOREIGN KEY FK_6F06A85DAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE followstory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE haveread
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likechapter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likestory
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_EB5604385E237E06 ON story (name)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE followstory (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_88B4877EA76ED395 (user_id), INDEX IDX_88B4877EAA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE haveread (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_7E57ABAC579F4768 (chapter_id), INDEX IDX_7E57ABACA76ED395 (user_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE likechapter (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_3E6B415F579F4768 (chapter_id), INDEX IDX_3E6B415FA76ED395 (user_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE likestory (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_6F06A85DA76ED395 (user_id), INDEX IDX_6F06A85DAA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory ADD CONSTRAINT FK_88B4877EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory ADD CONSTRAINT FK_88B4877EAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveread ADD CONSTRAINT FK_7E57ABAC579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveread ADD CONSTRAINT FK_7E57ABACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likechapter ADD CONSTRAINT FK_3E6B415F579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likechapter ADD CONSTRAINT FK_3E6B415FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory ADD CONSTRAINT FK_6F06A85DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory ADD CONSTRAINT FK_6F06A85DAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_story DROP FOREIGN KEY FK_5C322DDFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_story DROP FOREIGN KEY FK_5C322DDFAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follow_story DROP FOREIGN KEY FK_FEB2EE82A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follow_story DROP FOREIGN KEY FK_FEB2EE82AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_chapter DROP FOREIGN KEY FK_AA0CFF0DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE like_chapter DROP FOREIGN KEY FK_AA0CFF0D579F4768
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE have_read DROP FOREIGN KEY FK_C9222AAEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE have_read DROP FOREIGN KEY FK_C9222AAE579F4768
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE like_story
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE follow_story
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE like_chapter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE have_read
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_EB5604385E237E06 ON story
        SQL);
    }
}
