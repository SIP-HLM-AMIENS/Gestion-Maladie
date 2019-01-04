<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180827093550 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE arret (id INT AUTO_INCREMENT NOT NULL, employe_id INT NOT NULL, motif_id INT DEFAULT NULL, date_in DATE NOT NULL, date_out DATETIME DEFAULT NULL, visite_reprise TINYINT(1) NOT NULL, INDEX IDX_BBD1570E1B65292 (employe_id), INDEX IDX_BBD1570ED0EEB819 (motif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motif (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE arret ADD CONSTRAINT FK_BBD1570E1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE arret ADD CONSTRAINT FK_BBD1570ED0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE arret DROP FOREIGN KEY FK_BBD1570ED0EEB819');
        $this->addSql('DROP TABLE arret');
        $this->addSql('DROP TABLE motif');
    }
}
