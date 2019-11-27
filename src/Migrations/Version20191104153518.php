<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104153518 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE maintien (id INT AUTO_INCREMENT NOT NULL, employe_id INT DEFAULT NULL, date_creation DATE NOT NULL, date_fin DATE NOT NULL, nb100 INT NOT NULL, nb50 INT NOT NULL, INDEX IDX_1B2FAE351B65292 (employe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maintien ADD CONSTRAINT FK_1B2FAE351B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE service CHANGE responsable_id responsable_id INT DEFAULT NULL, CHANGE direction_id direction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arret ADD maintien_id INT DEFAULT NULL, CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT NULL, CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arret ADD CONSTRAINT FK_BBD1570EDFB44E6E FOREIGN KEY (maintien_id) REFERENCES maintien (id)');
        $this->addSql('CREATE INDEX IDX_BBD1570EDFB44E6E ON arret (maintien_id)');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL, CHANGE date_sortie date_sortie DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE user_id user_id INT DEFAULT NULL, CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE arret DROP FOREIGN KEY FK_BBD1570EDFB44E6E');
        $this->addSql('DROP TABLE maintien');
        $this->addSql('DROP INDEX IDX_BBD1570EDFB44E6E ON arret');
        $this->addSql('ALTER TABLE arret DROP maintien_id, CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT \'NULL\', CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE user_id user_id INT DEFAULT NULL, CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL, CHANGE date_sortie date_sortie DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service CHANGE responsable_id responsable_id INT DEFAULT NULL, CHANGE direction_id direction_id INT DEFAULT NULL');
    }
}
