<?php
// migrations/Version20241001185004.php
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241001185004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create PurchasedCourse table';
    }

    public function up(Schema $schema): void
    {
        // Add the SQL statement to create the 'purchased_course' table
        $this->addSql('CREATE TABLE purchased_course (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, course_id INT NOT NULL, purchase_date DATETIME NOT NULL, INDEX IDX_USER_ID (user_id), INDEX IDX_COURSE_ID (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_USER_ID FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchased_course ADD CONSTRAINT FK_COURSE_ID FOREIGN KEY (course_id) REFERENCES course (id)');
    }

    public function down(Schema $schema): void
    {
        // Add the SQL statement to drop the 'purchased_course' table
        $this->addSql('DROP TABLE purchased_course');
    }
}