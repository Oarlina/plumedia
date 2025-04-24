<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424130053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE likeChapter (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_F1D678C3A76ED395 (user_id), INDEX IDX_F1D678C3579F4768 (chapter_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE haveRead (user_id INT NOT NULL, chapter_id INT NOT NULL, INDEX IDX_DE650492A76ED395 (user_id), INDEX IDX_DE650492579F4768 (chapter_id), PRIMARY KEY(user_id, chapter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeChapter ADD CONSTRAINT FK_F1D678C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeChapter ADD CONSTRAINT FK_F1D678C3579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveRead ADD CONSTRAINT FK_DE650492A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveRead ADD CONSTRAINT FK_DE650492579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory RENAME INDEX idx_aec78759a76ed395 TO IDX_6F06A85DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory RENAME INDEX idx_aec78759aa5d4036 TO IDX_6F06A85DAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory RENAME INDEX idx_4975a87aa76ed395 TO IDX_88B4877EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory RENAME INDEX idx_4975a87aaa5d4036 TO IDX_88B4877EAA5D4036
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE likeChapter DROP FOREIGN KEY FK_F1D678C3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likeChapter DROP FOREIGN KEY FK_F1D678C3579F4768
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveRead DROP FOREIGN KEY FK_DE650492A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haveRead DROP FOREIGN KEY FK_DE650492579F4768
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likeChapter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE haveRead
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory RENAME INDEX idx_88b4877ea76ed395 TO IDX_4975A87AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE followstory RENAME INDEX idx_88b4877eaa5d4036 TO IDX_4975A87AAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory RENAME INDEX idx_6f06a85da76ed395 TO IDX_AEC78759A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likestory RENAME INDEX idx_6f06a85daa5d4036 TO IDX_AEC78759AA5D4036
        SQL);
    }
}
