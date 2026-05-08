<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507073034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Contact: ajout service_type, property_type (devis flow) + intervention_deadline rendu nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact ADD service_type VARCHAR(30) DEFAULT NULL, ADD property_type VARCHAR(30) DEFAULT NULL, CHANGE intervention_deadline intervention_deadline VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP service_type, DROP property_type, CHANGE intervention_deadline intervention_deadline VARCHAR(255) NOT NULL');
    }
}
