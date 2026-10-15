<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205093621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_media DROP FOREIGN KEY `FK_FB80826BCCD7E912`');
        $this->addSql('ALTER TABLE menu_media DROP FOREIGN KEY `FK_FB80826BEA9FDD75`');
        $this->addSql('DROP TABLE menu_media');
        $this->addSql('ALTER TABLE menu ADD media_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93EA9FDD75 ON menu (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_media (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, is_cover TINYINT NOT NULL, menu_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_FB80826BEA9FDD75 (media_id), INDEX IDX_FB80826BCCD7E912 (menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE menu_media ADD CONSTRAINT `FK_FB80826BCCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_media ADD CONSTRAINT `FK_FB80826BEA9FDD75` FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93EA9FDD75');
        $this->addSql('DROP INDEX IDX_7D053A93EA9FDD75 ON menu');
        $this->addSql('ALTER TABLE menu DROP media_id');
    }
}
