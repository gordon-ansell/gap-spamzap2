<?php
/**
 * GreenFedora PHP Library.
 *
 * @copyright   Gordon Ansell, 2017.
 */
 
declare(strict_types=1);
namespace GreenFedora\Db\Driver\Stmt;

use GreenFedora\Db\Driver\Stmt\StmtInterface;
use GreenFedora\Db\Driver\Stmt\AbstractStmt;

use GreenFedora\Db\Driver\Stmt\Exception\DbStmtException;

use PDO;
use PDOStatement;

/**
 * Database PDO statement.
 */
class PdoStmt extends AbstractStmt implements StmtInterface
{
    /**
     * Underlying PDO statement.
     * @var PDOStatement
     */
    protected $pdoStmt = null;

    /**
     * Constructor.
     *
     * @param   PDOStatement    $pdoStmt        PDO statement.
     * @return  void
     */
    public function __construct(PDOStatement $pdoStmt)
    {
        parent::__construct();
        $this->pdoStmt = $pdoStmt;
    }

    /**
     * Bind a value.
     *
     * @param   mixed       $param      Parameter to bind value to.
     * @param   mixed       $value      Value to bind.
     * @param   int|null    $type       Value type.
     * @return  bool
     * @throws  DbStmtException
     */
    public function bindValue($param, $value, ?int $type = null) : bool
    {
        if (is_null($type)) {
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } else if (is_null($value)) {
                $type = PDO::PARAM_NULL;
            } else {
                $type = PDO::PARAM_STR;
            }
        }

        if (!$this->pdoStmt->bindValue($param, $value, $type)) {
            throw new DbStmtException(sprintf("Unable to bind parameter '%s'", $param));
            return false;
        }
        return true;
    }

    /**
     * Fetch a column.
     *
     * @param   int         $offset         Offset.
     * @return  mixed
     * @throws  DbStmtException
     */
    public function fetchColumn(int $offset = 0)
    {
        try {
            $this->pdoStmt->execute();
            $result = $this->pdoStmt->fetchColumn($offset);
        } catch (\PDOException $exception) {
            $err = $this->pdoStmt->errorInfo();
            throw new DbStmtException(sprintf("fetchColumn failed: %s [%s/%s]", $err[2], $err[0], $err[1]));
        }
        return $result;
    }

    /**
     * Fetch an array of data.
     *
     * @return  array
     * @throws  DbStmtException
     */
    public function fetchArray() : array
    {
        try {
            $this->pdoStmt->execute();
            $result = $this->pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            $err = $this->pdoStmt->errorInfo();
            throw new DbStmtException(sprintf("fetchArray failed: %s [%s/%s]", $err[2], $err[0], $err[1]));
        }
        return $result;
    }

    /**
     * Execute prepared statement.
     *
     * @param   array       $params         Bound parameters.
     * @return  bool
     * @throws  DbStmtException
     */
    public function execute(array $params = array()) : bool
    {
        if (empty($params)) {
            $result = $this->pdoStmt->execute();
        } else {
            $result = $this->pdoStmt->execute($params);
        }
        if (!$result) {
            $err = $this->pdoStmt->errorInfo();
            throw new DbStmtException(sprintf("Execute failed: %s [%s/%s]", $err[2], $err[0], $err[1]));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the number of rows affected.
     * 
     * @return  int
     */
    public function affectedRows()
    {
        return $this->pdoStmt->rowCount();
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
        return call_user_func_array(array(&$this->pdoStmt, $func), $args);
    }
}
