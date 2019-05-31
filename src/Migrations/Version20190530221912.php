<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190530221912 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        static $tables = [
            'symfony_demo_comment',
            'symfony_demo_post',
            'symfony_demo_post_tag',
            'symfony_demo_tag',
            'symfony_demo_user'
        ];

        $this->addSql('CREATE SCHEMA history');
        $this->addSql('CREATE SCHEMA timetravel');

        array_map([$this, 'addSql'], \array_map(function (string $table) {
            return sprintf('ALTER TABLE %s ADD COLUMN sys_period tstzrange NOT NULL DEFAULT tstzrange(current_timestamp, null)', $table);
        }, $tables));

        array_map([$this, 'addSql'], \array_map(function (string $table) {
            return sprintf('create table history.%s (LIKE %s)', $table, $table);
        }, $tables));

        array_map([$this, 'addSql'], \array_map(function (string $table) {
            return sprintf('CREATE VIEW timetravel.%s AS SELECT * FROM %s UNION ALL SELECT * FROM history.%s', $table, $table, $table);
        }, $tables));

        array_map([$this, 'addSql'], \array_map(function (string $table) {
            return sprintf("CREATE TRIGGER versioning_trigger BEFORE INSERT OR UPDATE OR DELETE ON %s FOR EACH ROW EXECUTE PROCEDURE versioning('sys_period', 'history.$table', true)", $table, $table);
        }, $tables));
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
