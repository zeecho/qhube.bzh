<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413085457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE votes_cdf (voter VARCHAR(10) NOT NULL, three VARCHAR(10) NOT NULL, two VARCHAR(10) NOT NULL, four VARCHAR(10) NOT NULL, five VARCHAR(10) NOT NULL, oh VARCHAR(10) NOT NULL, bld VARCHAR(10) NOT NULL, pyra VARCHAR(10) NOT NULL, skewb VARCHAR(10) NOT NULL, PRIMARY KEY(voter)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE votes_cdf');
    }
}
