<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180903094845 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('CREATE TABLE prolongation (id INT AUTO_INCREMENT NOT NULL, arret_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, date_in DATE NOT NULL, date_out DATE NOT NULL, INDEX IDX_EBBF990768F1C150 (arret_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        //$this->addSql('ALTER TABLE prolongation ADD CONSTRAINT FK_EBBF990768F1C150 FOREIGN KEY (arret_id) REFERENCES arret (id)');
        //$this->addSql('ALTER TABLE employe RENAME INDEX fk_f804d3b98fbeecc4 TO IDX_F804D3B98FBEECC4');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE prolongation');
        //$this->addSql('ALTER TABLE employe RENAME INDEX idx_f804d3b98fbeecc4 TO FK_F804D3B98FBEECC4');
    }
}
