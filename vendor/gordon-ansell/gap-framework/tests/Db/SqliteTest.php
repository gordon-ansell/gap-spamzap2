<?php 
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Tests\Db\Sqlite;

use PHPUnit\Framework\TestCase;
use GreenFedora\Db\Db;
use GreenFedora\Db\Driver\PdoDriver;
use GreenFedora\Db\Platform\SQLite;
use GreenFedora\Stdlib\Arr\Arr;

use GreenFedora\Tests\Db\Schema\Tablespec;

use function PHPUnit\Framework\assertEquals;

/**
 * Tests for this package.
 */
final class SqliteTest extends TestCase
{
    public static $db = null;
    public $schema = null;

    public static function setUpBeforeClass(): void
    {
        $cfgArray = [
            'platform'  =>  'sqlite',
            'path'      =>  __DIR__ . '/data/sqlite.db'
        ];
        $cfg = new Arr($cfgArray);
        $driver = new PdoDriver($cfg);
        $platform = new Sqlite($cfg);
        self::$db = new Db($driver, $platform, $cfg);
        self::$db->driver()->connect();
    }

    public function setUp(): void
    {
        $this->schema = new TableSpec(self::$db);
    }

    public function testTableCreation()
    {
        $this->schema->createAllTables();
    }

    public function testTableInsert()
    {
        $data = [
            'dt'        => (new \DateTime())->format(\DateTimeInterface::ISO8601),
            'nonsense'  => "Just nonsense"
        ];
        $insert = self::$db->insert('table2', $data);
        $result = $insert->prepare()->execute();
        $select = self::$db->select('table2')->fetchArray();

        assertEquals("Just nonsense", $select[0]['nonsense']);
    }

    public function testLinkedTables()
    {
        $this->schema->dropAllTables();
        $this->schema->createAllTables();

        $table2 = array(
            [
                'dt'        => (new \DateTime())->format(\DateTimeInterface::ISO8601),
                'nonsense'  => "the first"
            ],
            [
                'dt'        => (new \DateTime())->format(\DateTimeInterface::ISO8601),
                'nonsense'  => "the second"
            ]
        );

        foreach ($table2 as $item) {
            self::$db->insert('table2', $item)->prepare()->execute();
        }

        $table1 = array(
            [
                'dt'            => (new \DateTime())->format(\DateTimeInterface::ISO8601),
                'fk_table2_id'  => 2,
                'iplong'        => 9876543,
                'raw'           => "raw text"
            ]
        );

        self::$db->insert('table1', $table1)->prepare()->execute();

        $result = self::$db->select('table1')->join('table2', 'table2_id', 'table1', 'fk_table2_id')->fetchArray();

        assertEquals("the second", $result[0]['nonsense']);

        $this->schema->dropAllTables();
    }

    public function tearDown(): void
    {
        //$this->schema->dropAllTables();
    }

    public static function tearDownAfterClass(): void
    {
    }
}