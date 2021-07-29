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
namespace GreenFedora\Db\Driver\Stmt;

/**
 * Database statement interface.
 */
interface StmtInterface
{
    /**
     * Bind a value.
     *
     * @param   mixed       $param      Parameter to bind value to.
     * @param   mixed       $value      Value to bind.
     * @param   int|null    $type       Value type.
     * @return  bool
     * @throws  DbStmtException
     */
    public function bindValue($param, $value, ?int $type = null) : bool;

    /**
     * Execute prepared statement.
     *
     * @param   array       $params         Bound parameters.
     * @return  bool
     * @throws  DbStmtException
     */
    public function execute(array $params = array()) : bool;

    /**
     * Fetch a column.
     *
     * @param   int         $offset         Offset.
     * @return  mixed
     */
    public function fetchColumn(int $offset = 0);

    /**
     * Fetch an array of data.
     *
     * @return  array
     */
    public function fetchArray() : array;

    /**
     * Get the number of rows affected.
     * 
     * @return  int
     */
    public function affectedRows();
}
