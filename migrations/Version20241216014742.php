<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216014742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_rating ADD CONSTRAINT FK_76B1E76F591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE course_rating ADD CONSTRAINT FK_76B1E76FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_76B1E76F591CC992 ON course_rating (course_id)');
        $this->addSql('CREATE INDEX IDX_76B1E76FA76ED395 ON course_rating (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_rating DROP FOREIGN KEY FK_76B1E76F591CC992');
        $this->addSql('ALTER TABLE course_rating DROP FOREIGN KEY FK_76B1E76FA76ED395');
        $this->addSql('DROP INDEX IDX_76B1E76F591CC992 ON course_rating');
        $this->addSql('DROP INDEX IDX_76B1E76FA76ED395 ON course_rating');
    }
}
