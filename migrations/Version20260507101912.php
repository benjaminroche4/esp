<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507101912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Split: créer table quote_request (devis flow), retirer service_type/property_type de contact, restaurer contact_type/intervention_deadline NOT NULL';
    }

    public function up(Schema $schema): void
    {
        // Nettoyer les anciennes lignes du devis flow (avant le split) qui auraient
        // contact_type/intervention_deadline NULL et bloqueraient le retour à NOT NULL.
        $this->addSql("DELETE FROM contact WHERE contact_type IS NULL OR intervention_deadline IS NULL");

        $this->addSql('CREATE TABLE quote_request (
            id INT AUTO_INCREMENT NOT NULL,
            service_type VARCHAR(30) NOT NULL,
            property_type VARCHAR(30) NOT NULL,
            city VARCHAR(120) NOT NULL,
            zip_code VARCHAR(10) NOT NULL,
            first_name VARCHAR(60) NOT NULL,
            last_name VARCHAR(60) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone_number VARCHAR(30) NOT NULL,
            message LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4');

        $this->addSql('ALTER TABLE contact
            DROP service_type,
            DROP property_type,
            CHANGE contact_type contact_type VARCHAR(100) NOT NULL,
            CHANGE intervention_deadline intervention_deadline VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE quote_request');
        $this->addSql('ALTER TABLE contact
            ADD service_type VARCHAR(30) DEFAULT NULL,
            ADD property_type VARCHAR(30) DEFAULT NULL,
            CHANGE contact_type contact_type VARCHAR(100) DEFAULT NULL,
            CHANGE intervention_deadline intervention_deadline VARCHAR(255) DEFAULT NULL');
    }
}
