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
namespace GreenFedora\Db\Schema;

use GreenFedora\Db\Schema\TableInterface;
use GreenFedora\Db\Schema\Exception\DbSchemaException;
use GreenFedora\Db\Schema\Exception\InvalidArgumentException;

/**
 * Database schema interface.
 */
interface SchemaInterface
{

    /**
     * Add a message.
     * 
     * @param   string      $msg            Message to add.
     * @return  void
     */
    public function addMsg(string $msg);

    /**
     * Get all the messages.
     * 
     * @return  array
     */
    public function getMsgs();

    /**
     * Auto the database.
     *
     * @param   bool        $forceNew       Force new install.
     * @return  bool
     * @throws  DbSchemaException
     */
    public function auto(bool $forceNew = false);

    /**
     * Create all tables.
     *
     * @return  void
     */
    public function createAllTables();

    /**
     * Drop all tables.
     *
     * @return  void
     */
    public function dropAllTables();

    /**
     * Add a table.
     *
     * @param   string|TableInterface   $table      Table name or obkect.
     * @param   array                   $props      Table properties.
     * @return  TableInterface
     */
    public function addTable($table, array $props = array()) : TableInterface;

    /**
     * Get a table.
     *
     * @param   string          $name           Table name.
     * @return  TableInterface
     * @throws  InvalidArgumentException
     */
    public function getTable(string $name) : TableInterface;

    /**
     * Check to see if the schema exists.
     *
     * @return  string|false
     */
    public function schemaExists();
}
