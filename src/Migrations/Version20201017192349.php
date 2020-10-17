<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017192349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, enabled TINYINT(1) NOT NULL, confirmation_token VARCHAR(30) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE crew_member (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name_en VARCHAR(255) NOT NULL, name_bg VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, title_bg VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, picture_url VARCHAR(255) NOT NULL, link_label VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE map_case (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name_en VARCHAR(180) NOT NULL, name_bg VARCHAR(180) NOT NULL, description_en VARCHAR(500) NOT NULL, description_bg VARCHAR(500) NOT NULL, link VARCHAR(200) DEFAULT NULL, longitude DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, picture_url VARCHAR(200) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, link VARCHAR(400) NOT NULL, source VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE screening (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', start DATETIME NOT NULL, name_en VARCHAR(255) NOT NULL, name_bg VARCHAR(255) NOT NULL, venue_name_en VARCHAR(255) NOT NULL, venue_name_bg VARCHAR(255) NOT NULL, venue_link VARCHAR(255) DEFAULT NULL, event_link VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE crew_member');
        $this->addSql('DROP TABLE map_case');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE screening');
    }
}
