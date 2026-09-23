<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260908101123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('DROP INDEX email ON user');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(15) NOT NULL, CHANGE is_verified is_verified TINYINT NOT NULL, CHANGE is_blocked is_blocked TINYINT NOT NULL, CHANGE confirmation_code confirmation_code VARCHAR(65) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE role role VARCHAR(15) NOT NULL, CHANGE avatar_url avatar_url VARCHAR(255) NOT NULL, CHANGE location location VARCHAR(255) NOT NULL, CHANGE birthday birthday DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(225) NOT NULL, CHANGE email email VARCHAR(225) NOT NULL, CHANGE password password VARCHAR(225) NOT NULL, CHANGE status status ENUM(\'unverified\', \'active\', \'blocked\') DEFAULT \'unverified\' NOT NULL, CHANGE is_verified is_verified TINYINT DEFAULT 0 NOT NULL, CHANGE is_blocked is_blocked TINYINT DEFAULT 0 NOT NULL, CHANGE confirmation_code confirmation_code VARCHAR(225) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE role role ENUM(\'candidate\', \'recruiter\', \'administrator\') DEFAULT \'candidate\' NOT NULL, CHANGE avatar_url avatar_url VARCHAR(225) DEFAULT NULL, CHANGE location location VARCHAR(225) NOT NULL, CHANGE birthday birthday DATE NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email ON user (email)');
    }
}
