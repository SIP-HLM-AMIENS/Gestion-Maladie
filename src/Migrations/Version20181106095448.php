<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181106095448 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE arret ADD clos TINYINT(1) NOT NULL');
        //$this->addSql('ALTER TABLE employe RENAME INDEX fk_f804d3b98fbeecc4 TO IDX_F804D3B98FBEECC4');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE arret DROP clos');
        //$this->addSql('ALTER TABLE employe RENAME INDEX idx_f804d3b98fbeecc4 TO FK_F804D3B98FBEECC4');
    }
}
