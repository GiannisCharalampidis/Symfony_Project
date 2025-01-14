<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208000631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course ADD text LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE purchased_course DROP FOREIGN KEY FK_USER_ID');
        $this->addSql('ALTER TABLE purchased_course DROP FOREIGN KEY FK_COURSE_ID');
        $this->addSql('DROP INDEX idx_user_id ON purchased_course');
        $this->addSql('CREATE INDEX IDX_2CD00D68A76ED395 ON purchased_course (user_id)');
        $this->addSql('DROP INDEX idx_course_id ON purchased_course');
        $this->addSql('CREATE INDEX IDX_2CD00D68591CC992 ON purchased_course (course_id)');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_USER_ID FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_COURSE_ID FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP text');
        $this->addSql('ALTER TABLE purchased_course DROP FOREIGN KEY FK_2CD00D68A76ED395');
        $this->addSql('ALTER TABLE purchased_course DROP FOREIGN KEY FK_2CD00D68591CC992');
        $this->addSql('DROP INDEX idx_2cd00d68591cc992 ON purchased_course');
        $this->addSql('CREATE INDEX IDX_COURSE_ID ON purchased_course (course_id)');
        $this->addSql('DROP INDEX idx_2cd00d68a76ed395 ON purchased_course');
        $this->addSql('CREATE INDEX IDX_USER_ID ON purchased_course (user_id)');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_2CD00D68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_2CD00D68591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) DEFAULT NULL');
    }
}
