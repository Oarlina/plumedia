<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424125102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE likeStory (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_AEC78759A76ED395 (user_id), INDEX IDX_AEC78759AA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE followStory (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_4975A87AA76ED395 (user_id), INDEX IDX_4975A87AAA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeStory ADD CONSTRAINT FK_AEC78759A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeStory ADD CONSTRAINT FK_AEC78759AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followStory ADD CONSTRAINT FK_4975A87AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followStory ADD CONSTRAINT FK_4975A87AAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_story DROP FOREIGN KEY FK_994FF60A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_story DROP FOREIGN KEY FK_994FF60AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_story
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_story (user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_994FF60A76ED395 (user_id), INDEX IDX_994FF60AA5D4036 (story_id), PRIMARY KEY(user_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_story ADD CONSTRAINT FK_994FF60A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_story ADD CONSTRAINT FK_994FF60AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeStory DROP FOREIGN KEY FK_AEC78759A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeStory DROP FOREIGN KEY FK_AEC78759AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followStory DROP FOREIGN KEY FK_4975A87AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followStory DROP FOREIGN KEY FK_4975A87AAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likeStory
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE followStory
        SQL);
    }
}
