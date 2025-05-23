<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413201659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64963ED1D03 ON user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494EEFEBA3 ON user (wca_website_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D6494EEFEBA3 ON user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64963ED1D03 ON user (wca_id)');
    }
}
