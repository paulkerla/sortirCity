<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105105908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, zipcode VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meetup (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, site_id INT NOT NULL, place_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(50) NOT NULL, startdatetime DATETIME NOT NULL, duration INT DEFAULT NULL, registrationlimitdate DATETIME NOT NULL, maxregistrations INT NOT NULL, meetupinfos LONGTEXT DEFAULT NULL, INDEX IDX_9377E28876C4DDA (organizer_id), INDEX IDX_9377E28F6BD1646 (site_id), INDEX IDX_9377E28DA6A219 (place_id), INDEX IDX_9377E285D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meetup_user (meetup_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5A015D64591E2316 (meetup_id), INDEX IDX_5A015D64A76ED395 (user_id), PRIMARY KEY(meetup_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(50) NOT NULL, streetname VARCHAR(50) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_741D53CD8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, surname VARCHAR(50) NOT NULL, phonenumber VARCHAR(10) NOT NULL, city VARCHAR(50) NOT NULL, avatarurl VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649F6BD1646 (site_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E28876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E28F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E28DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE meetup ADD CONSTRAINT FK_9377E285D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE meetup_user ADD CONSTRAINT FK_5A015D64591E2316 FOREIGN KEY (meetup_id) REFERENCES meetup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meetup_user ADD CONSTRAINT FK_5A015D64A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28876C4DDA');
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28F6BD1646');
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E28DA6A219');
        $this->addSql('ALTER TABLE meetup DROP FOREIGN KEY FK_9377E285D83CC1');
        $this->addSql('ALTER TABLE meetup_user DROP FOREIGN KEY FK_5A015D64591E2316');
        $this->addSql('ALTER TABLE meetup_user DROP FOREIGN KEY FK_5A015D64A76ED395');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD8BAC62AF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F6BD1646');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE meetup');
        $this->addSql('DROP TABLE meetup_user');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
