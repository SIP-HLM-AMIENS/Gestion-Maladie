<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190221150509 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, arret_id INT DEFAULT NULL, date DATETIME NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_67F068BCA76ED395 (user_id), INDEX IDX_67F068BC68F1C150 (arret_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC68F1C150 FOREIGN KEY (arret_id) REFERENCES arret (id)');
        $this->addSql('ALTER TABLE service CHANGE responsable_id responsable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arret CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT NULL, CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL');
        //$this->addSql('ALTER TABLE employe RENAME INDEX fk_f804d3b98fbeecc4 TO IDX_F804D3B98FBEECC4');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE commentaire');
        $this->addSql('ALTER TABLE arret CHANGE motif_id motif_id INT DEFAULT NULL, CHANGE date_out date_out DATE DEFAULT \'NULL\', CHANGE rcent rcent INT DEFAULT NULL, CHANGE rcinquante rcinquante INT DEFAULT NULL, CHANGE rzero rzero INT DEFAULT NULL, CHANGE rcarence rcarence INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe CHANGE service_id service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employe RENAME INDEX idx_f804d3b98fbeecc4 TO FK_F804D3B98FBEECC4');
        $this->addSql('ALTER TABLE ijss CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prolongation CHANGE arret_id arret_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service CHANGE responsable_id responsable_id INT DEFAULT NULL');
    }
}
