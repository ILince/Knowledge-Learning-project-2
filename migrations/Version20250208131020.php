<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208131020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, theme_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, obtained_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6C3C6D75A76ED395 (user_id), INDEX IDX_6C3C6D7559027487 (theme_id), INDEX IDX_6C3C6D75DE12AB56 (created_by), INDEX IDX_6C3C6D7516FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, theme_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_169E6FB959027487 (theme_id), INDEX IDX_169E6FB9DE12AB56 (created_by), INDEX IDX_169E6FB916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F87474F3591CC992 (course_id), INDEX IDX_F87474F3DE12AB56 (created_by), INDEX IDX_F87474F316FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_progress (user_id INT NOT NULL, lesson_id INT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, validated TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6A46B85FA76ED395 (user_id), INDEX IDX_6A46B85FCDF80196 (lesson_id), INDEX IDX_6A46B85FDE12AB56 (created_by), INDEX IDX_6A46B85F16FE72E1 (updated_by), PRIMARY KEY(user_id, lesson_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_9775E708DE12AB56 (created_by), INDEX IDX_9775E70816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, delivery_address VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_8D93D649DE12AB56 (created_by), INDEX IDX_8D93D64916FE72E1 (updated_by), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_lesson (user_id INT NOT NULL, lesson_id INT NOT NULL, INDEX IDX_9D266FCEA76ED395 (user_id), INDEX IDX_9D266FCECDF80196 (lesson_id), PRIMARY KEY(user_id, lesson_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_course (user_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_73CC7484A76ED395 (user_id), INDEX IDX_73CC7484591CC992 (course_id), PRIMARY KEY(user_id, course_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7559027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7516FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB959027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F316FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85FDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson_progress ADD CONSTRAINT FK_6A46B85F16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E70816FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_course ADD CONSTRAINT FK_73CC7484591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75A76ED395');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7559027487');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75DE12AB56');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7516FE72E1');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB959027487');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9DE12AB56');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB916FE72E1');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3591CC992');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3DE12AB56');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F316FE72E1');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FA76ED395');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FCDF80196');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85FDE12AB56');
        $this->addSql('ALTER TABLE lesson_progress DROP FOREIGN KEY FK_6A46B85F16FE72E1');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708DE12AB56');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E70816FE72E1');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DE12AB56');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64916FE72E1');
        $this->addSql('ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCEA76ED395');
        $this->addSql('ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCECDF80196');
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484A76ED395');
        $this->addSql('ALTER TABLE user_course DROP FOREIGN KEY FK_73CC7484591CC992');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_progress');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_lesson');
        $this->addSql('DROP TABLE user_course');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
