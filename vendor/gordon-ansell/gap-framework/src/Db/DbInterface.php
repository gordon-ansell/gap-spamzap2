<?php
/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);
namespace GreenFedora\Db;

use GreenFedora\Db\Driver\DriverInterface;
use GreenFedora\Db\Platform\PlatformInterface;
use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\InsertInterface;
use GreenFedora\Db\Sql\UpdateInterface;
use GreenFedora\Db\Sql\SelectInterface;
use GreenFedora\Db\Sql\DeleteInterface;

use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * Database class interface.
 */
interface DbInterface
{
    /**
     * Get the driver.
     *
     * @return  DriverInterface
     */
    public function driver() : DriverInterface;

    /**
     * Get the platform.
     *
     * @return  PlatformInterface
     */
    public function platform() : PlatformInterface;

    /**
     * Create an insert statement.
     *
     * @param   string      $table      Table to insert into.
     * @param   array       $colVals    Column => values.
     * @return  InsertInterface
     */
    public function insert(string $table, array $colVals) : InsertInterface;

    /**
     * Return the insert ID.
     * 
     * @return  int
     */
    public function insertId() : int;

    /**
     * Create an update statement.
     *
     * @param   string      $table      Table to insert into.
     * @param   array       $colVals    Column => values.
     * @return  UpdateInterface
     */
    public function update(string $table, array $colVals) : UpdateInterface;

    /**
     * Create a delete statement.
     *
     * @param   string      $table      Table to delete from.
     * @param   array       $wheres     Column => values for where.
     * @return  DeleteInterface
     */
    public function delete(string $table, ?array $wheres = null) : DeleteInterface;

    /**
     * Truncate a table.
     *
     * @param   string      $table      Table to truncate.
     * @return  bool
     */
    public function truncate(string $table) : bool;

    /**
     * Create a select statement.
     *
     * @param   string|null     $from       From table.
     * @return  SelectInterface
     */
    public function select(?string $from = null) : SelectInterface;

    /**
     * Prepare a statement.
     *
     * @param   string      $sql        SQL to prepare.
     * @param   array       $options    Driver options.
     * @return  StmtInterface
     */
    public function prepare(string $sql, array $options = array()) : ?StmtInterface;

    /**
     * Quote an ID.
     *
     * @param   string      $raw        Raw string.
     * @return  string
     */
    public function idq(string $raw) : string;

    /**
     * Get a prefixed table name.
     *
     * @param   string      $raw        Raw table name.
     * @param   bool        $quote      Quote it?
     * @return  string
     */
    public function tn(string $raw, bool $quote = true) : string;

    /**
     * Get the config.
     * 
     * @return  ArrInterface
     */
    public function getConfig(): ArrInterface;
}
