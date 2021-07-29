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
 * Base CRUD model.
 */
abstract class AbstractCrudModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = null;

    /**
     * Is the ID a string?
     * @vard bool
     */
    protected $idIsString = false;

    /**
     * Get the database.
     * 
     * @return  DbInterface
     */
    abstract public function getDb(): DbInterface;

    /**
     * List all entries.
     * 
     * @param   string      $order  Column to order by.
     * @return  array
     */
    public function listAll(?string $order = null): array
    {
        if (is_null($order)) {
            $order = $this->tableName . '_id';
        }
        return $this->getDb()->select($this->tableName)
            ->order($order)
            ->fetchArray();
    }

    /**
     * Fetch a particular entry.
     * 
     * @param   mixed     $id     ID.      
     * @return  array
     */
    public function fetch($id): array
    {
        if (!$this->idIsString) $id = intval($id);
        return $this->getDb()->select($this->tableName)
            ->where($this->tableName . '_id', $id)
            ->fetchArray();
    }

    /**
     * Empty table and reset the primary key sequence.
     * 
     * @return  bool
     * @throws  DbModelException
     */
    public function reset(): bool
    {
        $result = $this->getDb()
            ->delete($this->tableName)
            ->execute();
        if (false === $result) {
            throw new DbModelException(sprintf("Failed to delete all %s records.", $this->tableName));
            return false;
        }

        return true;
    }

    /**
     * See if we have an entry for a given ID.
     * 
     * @param   mixed     $id    ID to test.
     * @return  bool
     */
    public function hasEntry($id): bool
    {
        if (!$this->idIsString) $id = intval($id);
        $result = $this->getDb()
            ->select($this->tableName)
            ->where($this->tableName . '_id', $id)
            ->fetchArray();

        return (count($result) > 0);
    }

    /**
     * Create an entry.
     * 
     * @param   array         $data       Data to create record with.
     * @return  mixed                     Return the insert ID.
     * @throws  DbModelException
     */
    public function create(array $data)
    {
        $result = $this->getDb()
            ->insert($this->tableName, $data)
            ->execute();
        if (false === $result) {
            throw new DbModelException(sprintf("Failed to insert %s record.", $this->tableName));
            return false;
        }
        return $this->getDb()->insertId();
    }

    /**
     * Update an entry.
     * 
     * @param   mixed           $id         ID.
     * @param   array           $data       Data
     * @return  bool
     * @throws  DbModelException
     */
    public function update($id, array $data): bool
    {
        if (!$this->idIsString) $id = intval($id);
        $result = $this->getDb()
            ->update($this->tableName, $data)
            ->where($this->tableName . '_id', $id)
            ->execute();
        if (false === $result) {
            throw new DbModelException(sprintf("Failed to update %s record.", $this->tableName));
            return false;
        }
        return true;
    }

    /**
     * Delete an entry.
     * 
     * @param   mixed         $id        ID.
     * @return  bool
     * @throws  DbModelException
     */
    public function delete($id): bool
    {
        if (!$this->idIsString) $id = intval($id);
        $result = $this->getDb()
            ->delete($this->tableName, [$this->tableName . '_id' => $id])
            ->execute();
        if (false === $result) {
            throw new DbModelException(sprintf("Failed to delete %s record.", $this->tableName));
            return false;
        }
        return true;
    }
}