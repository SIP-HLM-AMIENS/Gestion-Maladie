<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191024143617 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE direction (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service ADD direction_id INT DEFAULT NULL, CHANGE responsable_id responsable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AF73D997 FOREIGN KEY (direction_id) REFERENCES direction (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2AF73D997 ON service (direction_id)');
        $this->addSql('ALTER TABLE arret CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT NULL, CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL, CHANGE date_sortie date_sortie DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE user_id user_id INT DEFAULT NULL, CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2AF73D997');
        $this->addSql('DROP TABLE direction');
        $this->addSql('ALTER TABLE arret CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT \'NULL\', CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE user_id user_id INT DEFAULT NULL, CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL, CHANGE date_sortie date_sortie DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_E19D9AD2AF73D997 ON service');
        $this->addSql('ALTER TABLE service DROP direction_id, CHANGE responsable_id responsable_id INT DEFAULT NULL');
    }
}
