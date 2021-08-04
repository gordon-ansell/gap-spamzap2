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
namespace GreenFedora\Db\Model;

use GreenFedora\Db\DbInterface;
use GreenFedora\Db\Model\Exception\DbModelException;

/**
 * Base CRUD model interface.
 */
interface CrudModelInterface
{
    /**
     * Get the database.
     * 
     * @return  DbInterface
     */
    public function getDb(): DbInterface;

    /**
     * List all entries.
     * 
     * @param   string      $order  Column to order by.
     * @param   string      $dir    Direction.
     * @return  array
     */
    public function listAll(?string $order = null, string $dir = 'asc'): array;

    /**
     * Fetch a particular entry.
     * 
     * @param   mixed     $id     ID.      
     * @return  array
     */
    public function fetch($id): array;

    /**
     * Empty table and reset the primary key sequence.
     * 
     * @return  bool
     * @throws  DbModelException
     */
    public function reset(): bool;

    /**
     * See if we have an entry for a given ID.
     * 
     * @param   mixed     $id    ID to test.
     * @return  bool
     */
    public function hasEntry($id): bool;

    /**
     * Create an entry.
     * 
     * @param   array         $data       Data to create record with.
     * @return  mixed
     * @throws  DbModelException
     */
    public function create(array $data);

    /**
     * Update an entry.
     * 
     * @param   mixed           $id         ID.
     * @param   array           $data       Data
     * @return  bool
     * @throws  DbModelException
     */
    public function update($id, array $data): bool;

    /**
     * Delete an entry.
     * 
     * @param   mixed         $id        ID.
     * @return  bool
     * @throws  DbModelException
     */
    public function delete($id): bool;
}