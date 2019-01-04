<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180827125417 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE coefficient (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, cadre TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employe ADD coeff_id INT NOT NULL, DROP coeff');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B98FBEECC4 FOREIGN KEY (coeff_id) REFERENCES coefficient (id)');
        $this->addSql('CREATE INDEX IDX_F804D3B98FBEECC4 ON employe (coeff_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B98FBEECC4');
        $this->addSql('DROP TABLE coefficient');
        $this->addSql('DROP INDEX IDX_F804D3B98FBEECC4 ON employe');
        $this->addSql('ALTER TABLE employe ADD coeff VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP coeff_id');
    }
}
