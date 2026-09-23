<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260910100555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE status status VARCHAR(15) NOT NULL, CHANGE is_verified is_verified TINYINT NOT NULL, CHANGE is_blocked is_blocked TINYINT NOT NULL, CHANGE role role VARCHAR(15) NOT NULL, CHANGE birthday birthday DATE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user CHANGE status status ENUM(\'unverified\', \'active\', \'blocked\') DEFAULT \'unverified\' NOT NULL, CHANGE is_verified is_verified TINYINT DEFAULT 0 NOT NULL, CHANGE is_blocked is_blocked TINYINT DEFAULT 0 NOT NULL, CHANGE role role ENUM(\'admin\', \'candidate\', \'recruiter\') DEFAULT \'candidate\' NOT NULL, CHANGE birthday birthday DATE NOT NULL');
    }
}
