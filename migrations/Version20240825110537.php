<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240825110537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mail_campaign (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sent_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_campaign_category (mail_campaign_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_27BE1B3242E68AA9 (mail_campaign_id), INDEX IDX_27BE1B3212469DE2 (category_id), PRIMARY KEY(mail_campaign_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mail_campaign_category ADD CONSTRAINT FK_27BE1B3242E68AA9 FOREIGN KEY (mail_campaign_id) REFERENCES mail_campaign (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mail_campaign_category ADD CONSTRAINT FK_27BE1B3212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail_campaign_category DROP FOREIGN KEY FK_27BE1B3242E68AA9');
        $this->addSql('ALTER TABLE mail_campaign_category DROP FOREIGN KEY FK_27BE1B3212469DE2');
        $this->addSql('DROP TABLE mail_campaign');
        $this->addSql('DROP TABLE mail_campaign_category');
    }
}
