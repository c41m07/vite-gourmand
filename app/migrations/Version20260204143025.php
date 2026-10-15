<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204143025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE allergen (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE contact_message (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, ip VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE customer_order (id INT AUTO_INCREMENT NOT NULL, ordered_at DATETIME NOT NULL, service_date DATE NOT NULL, service_time VARCHAR(255) NOT NULL, people_count INT NOT NULL, delivery_address VARCHAR(255) NOT NULL, delivery_city VARCHAR(255) NOT NULL, delivery_postal_code VARCHAR(255) NOT NULL, delivery_price INT NOT NULL, discount_amount INT DEFAULT NULL, total_price INT NOT NULL, note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, equipment_loan_id INT DEFAULT NULL, INDEX IDX_3B1CE6A3A76ED395 (user_id), UNIQUE INDEX UNIQ_3B1CE6A3B8084960 (equipment_loan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE customer_order_menu (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, line_total INT NOT NULL, customer_order_id INT NOT NULL, review_id INT DEFAULT NULL, menu_id INT NOT NULL, INDEX IDX_CD3D0406A15A2E17 (customer_order_id), INDEX IDX_CD3D04063E2E969B (review_id), INDEX IDX_CD3D0406CCD7E912 (menu_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE customer_order_status_history (id INT AUTO_INCREMENT NOT NULL, changed_at DATETIME DEFAULT NULL, comment LONGTEXT DEFAULT NULL, customer_order_id INT NOT NULL, changed_by_user_id INT DEFAULT NULL, order_status_id INT NOT NULL, INDEX IDX_9846584BA15A2E17 (customer_order_id), INDEX IDX_9846584BC20FFD1C (changed_by_user_id), INDEX IDX_9846584BD7707B45 (order_status_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE diet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE dish (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT NOT NULL, dish_type_id INT DEFAULT NULL, INDEX IDX_957D8CB855FB9605 (dish_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE dish_allergen (id INT AUTO_INCREMENT NOT NULL, dish_id INT NOT NULL, allergen_id INT NOT NULL, INDEX IDX_3C4389A5148EB0CB (dish_id), INDEX IDX_3C4389A56E775A4A (allergen_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE dish_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE equipment_loan (id INT AUTO_INCREMENT NOT NULL, loan_start_at DATETIME NOT NULL, loan_end_at DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, status_id INT NOT NULL, INDEX IDX_FF57DC056BF700BD (status_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE equipment_loan_status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, img_url VARCHAR(255) NOT NULL, alt_text VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, min_people INT NOT NULL, base_price INT NOT NULL, condition_info LONGTEXT DEFAULT NULL, stock INT NOT NULL, active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, theme_id INT DEFAULT NULL, diet_id INT DEFAULT NULL, INDEX IDX_7D053A9359027487 (theme_id), INDEX IDX_7D053A93E1E13ACE (diet_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu_dish (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, dish_id INT NOT NULL, INDEX IDX_5D327CF6CCD7E912 (menu_id), INDEX IDX_5D327CF6148EB0CB (dish_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE menu_media (id INT AUTO_INCREMENT NOT NULL, position INT NOT NULL, is_cover TINYINT NOT NULL, menu_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_FB80826BCCD7E912 (menu_id), INDEX IDX_FB80826BEA9FDD75 (media_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE opening_hour (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(255) NOT NULL, opens_at VARCHAR(255) NOT NULL, closes_at VARCHAR(255) NOT NULL, is_closed TINYINT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, rating INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, validated TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_794381C6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, postal_address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A3B8084960 FOREIGN KEY (equipment_loan_id) REFERENCES equipment_loan (id)');
        $this->addSql('ALTER TABLE customer_order_menu ADD CONSTRAINT FK_CD3D0406A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_order_menu ADD CONSTRAINT FK_CD3D04063E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        $this->addSql('ALTER TABLE customer_order_menu ADD CONSTRAINT FK_CD3D0406CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE customer_order_status_history ADD CONSTRAINT FK_9846584BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_order_status_history ADD CONSTRAINT FK_9846584BC20FFD1C FOREIGN KEY (changed_by_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer_order_status_history ADD CONSTRAINT FK_9846584BD7707B45 FOREIGN KEY (order_status_id) REFERENCES order_status (id)');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB855FB9605 FOREIGN KEY (dish_type_id) REFERENCES dish_type (id)');
        $this->addSql('ALTER TABLE dish_allergen ADD CONSTRAINT FK_3C4389A5148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id)');
        $this->addSql('ALTER TABLE dish_allergen ADD CONSTRAINT FK_3C4389A56E775A4A FOREIGN KEY (allergen_id) REFERENCES allergen (id)');
        $this->addSql('ALTER TABLE equipment_loan ADD CONSTRAINT FK_FF57DC056BF700BD FOREIGN KEY (status_id) REFERENCES equipment_loan_status (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9359027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93E1E13ACE FOREIGN KEY (diet_id) REFERENCES diet (id)');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT FK_5D327CF6CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_dish ADD CONSTRAINT FK_5D327CF6148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id)');
        $this->addSql('ALTER TABLE menu_media ADD CONSTRAINT FK_FB80826BCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE menu_media ADD CONSTRAINT FK_FB80826BEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3A76ED395');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A3B8084960');
        $this->addSql('ALTER TABLE customer_order_menu DROP FOREIGN KEY FK_CD3D0406A15A2E17');
        $this->addSql('ALTER TABLE customer_order_menu DROP FOREIGN KEY FK_CD3D04063E2E969B');
        $this->addSql('ALTER TABLE customer_order_menu DROP FOREIGN KEY FK_CD3D0406CCD7E912');
        $this->addSql('ALTER TABLE customer_order_status_history DROP FOREIGN KEY FK_9846584BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_status_history DROP FOREIGN KEY FK_9846584BC20FFD1C');
        $this->addSql('ALTER TABLE customer_order_status_history DROP FOREIGN KEY FK_9846584BD7707B45');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB855FB9605');
        $this->addSql('ALTER TABLE dish_allergen DROP FOREIGN KEY FK_3C4389A5148EB0CB');
        $this->addSql('ALTER TABLE dish_allergen DROP FOREIGN KEY FK_3C4389A56E775A4A');
        $this->addSql('ALTER TABLE equipment_loan DROP FOREIGN KEY FK_FF57DC056BF700BD');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9359027487');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93E1E13ACE');
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY FK_5D327CF6CCD7E912');
        $this->addSql('ALTER TABLE menu_dish DROP FOREIGN KEY FK_5D327CF6148EB0CB');
        $this->addSql('ALTER TABLE menu_media DROP FOREIGN KEY FK_FB80826BCCD7E912');
        $this->addSql('ALTER TABLE menu_media DROP FOREIGN KEY FK_FB80826BEA9FDD75');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP TABLE allergen');
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE customer_order_menu');
        $this->addSql('DROP TABLE customer_order_status_history');
        $this->addSql('DROP TABLE diet');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE dish_allergen');
        $this->addSql('DROP TABLE dish_type');
        $this->addSql('DROP TABLE equipment_loan');
        $this->addSql('DROP TABLE equipment_loan_status');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE menu_dish');
        $this->addSql('DROP TABLE menu_media');
        $this->addSql('DROP TABLE opening_hour');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
