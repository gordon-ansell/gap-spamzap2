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

use GreenFedora\Db\Driver\DriverInterface;
use GreenFedora\Db\Driver\AbstractDriver;

use GreenFedora\Db\Driver\Stmt\StmtInterface;
use GreenFedora\Db\Driver\Stmt\PdoStmt;

use GreenFedora\Db\Driver\Exception\DbDriverException;

use PDO;
use PDOException;

/**
 * Database PDO driver.
 */
class PdoDriver extends AbstractDriver implements DriverInterface
{
    /**
     * Underlying PDO.
     * @var PDO
     */
    protected $pdo = null;

    /**
     * Connect to the database.
     *
     * @return  void
     * @throws  DbDriverException
     */
    public function connect()
    {
        $platform = $this->config->get('platform', 'mysql');
        $user = null;
        $pass = null;

        if ('mysql' === $platform) {
            $user = $this->config->user;
            $pass = $this->config->pass;
            $dsn = 'mysql:dbname=' . $this->config->name . ';host=' . $this->config->host . ';port=' . $this->config->port;
        } else if ('sqlite' === $platform) {
            $dsn = 'sqlite:' . $this->config->path;
        } else {
            throw new DbDriverException(sprintf("'%s' is an unsupported platform.", $platform));
        }

        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            throw new DbDriverException("Could not connect to data base, error: " . $exception->getMessage());
        }
    }

    /**
     * Prepare a statement.
     *
     * @param   string      $sql        SQL to prepare.
     * @param   array       $options    Driver options.
     * @return  StmtInterface
     * @throws  DbDriverException
     */
    public function prepare(string $sql, array $options = array()) : ?StmtInterface
    {
        try {
            $sth = $this->pdo->prepare($sql, $options);
            return new PdoStmt($sth);
        } catch (PDOException $exception) {
            throw new DbDriverException("Db prepare error: " . $exception->getMessage() . PHP_EOL . $sql);
        }
        return null;
    }

    /**
     * Begin a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function beginTransaction() : bool
    {
        try {
            $this->pdo->beginTransaction();
            return true;
        } catch (PDOException $exception) {
            throw new DbDriverException('Db transaction error: ' . $exception->getMessage);
        }
        return false;
    }

    /**
     * Commit a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function commit() : bool
    {
        try {
            $this->pdo->commit();
            return true;
        } catch (PDOException $exception) {
            throw new DbDriverException('Db transaction error: ' . $exception->getMessage);
        }
        return false;
    }

    /**
     * Roll back a transaction.
     *
     * @return  bool
     * @throws  DbDriverException
     */
    public function rollBack() : bool
    {
        try {
            $this->pdo->rollBack();
            return true;
        } catch (PDOException $exception) {
            throw new DbDriverException('Db transaction error: ' . $exception->getMessage);
        }
        return false;
    }

    /**
     * Get the last insert ID.
     * 
     * @return  int
     */
    public function insertId() : int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Call PDO by default.
     *
     * @param   string      $func       Function.
     * @param   array       $args       Arguments.
     * @return  mixed
     */
    public function __call(string $func, array $args = array())
    {
        return call_user_func_array(array(&$this->pdo, $func), $args);
    }
}
