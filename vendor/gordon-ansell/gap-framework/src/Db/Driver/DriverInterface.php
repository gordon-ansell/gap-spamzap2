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
namespace GreenFedora\Db\Driver;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

/**
 * Database driver interface.
 */
interface DriverInterface
{
    /**
     * Connect to the database.
     *
     * @return  void
     * @throws  DbDriverException
     */
    public function connect();

    /**
     * Prepare a statement.
     *
     * @param   string      $sql        SQL to prepare.
     * @param   array       $options    Driver options.
     * @return  StmtInterface
     * @throws  DbStmtException
     */
    public function prepare(string $sql, array $options = array()) : ?StmtInterface;

    /**
     * Begin a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function beginTransaction() : bool;

    /**
     * Commit a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function commit() : bool;

    /**
     * Roll back a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function rollBack() : bool;

    /**
     * Get the last insert ID.
     * 
     * @return  int
     */
    public function insertId() : int;
}
