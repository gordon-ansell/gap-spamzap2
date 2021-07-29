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
namespace GreenFedora\Db\Schema;

use GreenFedora\Db\Schema\TableInterface;

/**
 * Database schema column interface.
 */
interface ColInterface
{

    /**
     * Get the name.
     *
     * @return  string
     */
    public function getName() : string;

    /**
     * Get the parent table.
     *
     * @return  TableInterface
     */
    public function getTable() : TableInterface;

    /**
     * Get a property or default.
     *
     * @param   string      $name       Property name.
     * @param   mixed       $default    Default.
     * @return  mixed
     */
    public function getProp(string $name, $default = null);

    /**
     * Get all properties.
     *
     * @return  array
     */
    public function getProps() : array;

    /**
     * Get the references SQL.
     *
     * @return  string
     * @throws  DbSchemaException
     */
    public function getReferencesSql() : string;

    /**
     * Get the column SQL
     *
     * @return  string
     * @throws  DbSchemaException
     */
    public function getSql() : string;
}
