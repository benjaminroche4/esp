<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507085746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Contact: rendre contact_type nullable (le quote flow ne le renseigne pas, seul le long form le fait)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact CHANGE contact_type contact_type VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact CHANGE contact_type contact_type VARCHAR(100) NOT NULL');
    }
}
