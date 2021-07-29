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
namespace GreenFedora\Db;

use GreenFedora\Db\DbInterface;
use GreenFedora\Db\Driver\DriverInterface;
use GreenFedora\Db\Platform\PlatformInterface;
use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Stdlib\Arr\ArrInterface;

use GreenFedora\Db\Sql\Insert;
use GreenFedora\Db\Sql\InsertInterface;
use GreenFedora\Db\Sql\Update;
use GreenFedora\Db\Sql\UpdateInterface;
use GreenFedora\Db\Sql\Select;
use GreenFedora\Db\Sql\SelectInterface;
use GreenFedora\Db\Sql\Delete;
use GreenFedora\Db\Sql\DeleteInterface;

/**
 * Database class.
 */
class Db implements DbInterface
{
    /**
     * Platform.
     * @var PlatformInterface
     */
    protected $platform = null;

    /**
     * Driver
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * Configs.
     * @var ArrInterface
     */
    protected $config = null;

    /**
     * Constructor.
     *
     * @param   DriverInterface      $driver     Database driver.
     * @param   PlatformInterface    $platform   Database platform.
     * @param   ArrInterface         $config     Configs.
     * @return  void
     * #[Inject (config: cfg|db)]
     */
    public function __construct(DriverInterface $driver, PlatformInterface $platform, ArrInterface $config = null)
    {
        $this->driver = $driver;
        $this->platform = $platform;
        $this->config = $config;
    }

    /**
     * Get the driver.
     *
     * @return  DriverInterface
     */
    public function driver() : DriverInterface
    {
        return $this->driver;
    }

    /**
     * Get the platform.
     *
     * @return  PlatformInterface
     */
    public function platform() : PlatformInterface
    {
        return $this->platform;
    }

    /**
     * Create an insert statement.
     *
     * @param   string      $table      Table to insert into.
     * @param   array       $colVals    Column => values.
     * @return  InsertInterface
     */
    public function insert(string $table, array $colVals) : InsertInterface
    {
        return new Insert($this, $table, $colVals);
    }

    /**
     * Return the insert ID.
     * 
     * @return  int
     */
    public function insertId() : int
    {
        return $this->driver()->insertId();
    }

    /**
     * Create an update statement.
     *
     * @param   string      $table      Table to insert into.
     * @param   array       $colVals    Column => values.
     * @return  UpdateInterface
     */
    public function update(string $table, array $colVals) : UpdateInterface
    {
        return new Update($this, $table, $colVals);
    }

    /**
     * Create a delete statement.
     *
     * @param   string      $table      Table to delete from.
     * @param   array       $wheres     Column => values for where.
     * @return  DeleteInterface
     */
    public function delete(string $table, ?array $wheres = null) : DeleteInterface
    {
        return new Delete($this, $table, $wheres);
    }

    /**
     * Truncate a table.
     *
     * @param   string      $table      Table to truncate.
     * @return  bool
     */
    public function truncate(string $table) : bool
    {
        $sql = 'TRUNCATE ' . $this->idq($table);
        return $this->driver->prepare($sql)->execute();
    }

    /**
     * Create a select statement.
     *
     * @param   string|null     $from       From table.
     * @return  SelectInterface
     */
    public function select(?string $from = null) : SelectInterface
    {
        return new Select($this, $from);
    }

    /**
     * Prepare a statement.
     *
     * @param   string      $sql        SQL to prepare.
     * @param   array       $options    Driver options.
     * @return  StmtInterface
     */
    public function prepare(string $sql, array $options = array()) : ?StmtInterface
    {
        return $this->driver()->prepare($sql, $options);
    }

    /**
     * Quote an ID.
     *
     * @param   string      $raw        Raw string.
     * @return  string
     */
    public function idq(string $raw) : string
    {
        return $this->platform()->idq($raw);
    }

    /**
     * Get a prefixed table name.
     *
     * @param   string      $raw        Raw table name.
     * @param   bool        $quote      Quote it?
     * @return  string
     */
    public function tn(string $raw, bool $quote = true) : string
    {
        return $this->platform()->tn($raw, $quote);
    }

    /**
     * Get the config.
     * 
     * @return  ArrInterface
     */
    public function getConfig(): ArrInterface
    {
        return $this->config;
    }
}
