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
namespace GreenFedora\Db\Sql;

use GreenFedora\Db\Sql\Exception\DbSqlException;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\Sql\SelectInterface;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\Part\WhereUser;
use GreenFedora\Db\Sql\Part\Where;
use GreenFedora\Db\Sql\Part\HavingUser;
use GreenFedora\Db\Sql\Part\Having;
use GreenFedora\Db\Sql\Part\LimitUser;
use GreenFedora\Db\Sql\Part\Limit;
use GreenFedora\Db\Sql\Part\OrderUser;
use GreenFedora\Db\Sql\Part\Order;
use GreenFedora\Db\Sql\Part\GroupUser;
use GreenFedora\Db\Sql\Part\Group;
use GreenFedora\Db\Sql\Part\TableReference;
use GreenFedora\Db\Sql\Part\SelectExpr;
use GreenFedora\Db\Sql\Part\Join;

use GreenFedora\Db\DbInterface;
use GreenFedora\Finder\Filter\FileNameStartsWith;
use GreenFedora\Finder\Filter\Set;
use GreenFedora\Finder\Finder;
use GreenFedora\Stdlib\Path;

/**
 * SQL select class.
 */
class Select extends AbstractSql implements SelectInterface
{
    use WhereUser;
    use LimitUser;
    use GroupUser;
    use OrderUser;
    use HavingUser;

    /**
     * Select expressions.
     * @var SelectExpr[]
     */
    protected $selectExprs = array();

    /**
     * Froms.
     * @var TableReference[]
     */
    protected $from = array();

    /**
     * Joins.
     * @var Join[]
     */
    protected $joins = array();

    /**
     * Cache SQL.
     * @var string
     */
    protected $cacheSql = null;

    /**
     * Cache tables.
     * @var array
     */
    protected $cacheTables = [];

    /**
     * Constructor.
     *
     * @param   DbInterface              $db         Database parent.
     * @param   string|null              $from       From table.
     * @return  void
     */
    public function __construct(DbInterface $db, ?string $from = null)
    {
        parent::__construct($db);

        $this->where = new Where($db);
        $this->limit = new Limit();
        $this->order = new Order($db);
        $this->group = new Group($db);
        $this->having = new Having($db);

        if (null !== $from) {
            $this->from($from);
        }
    }

    /**
     * Add a from.
     *
     * @param   string      $table      Table.
     * @param   string|null $alias      Table alias.
     * @return  SelectInterface
     */
    public function from(string $table, ?string $alias = null): SelectInterface
    {
        $this->from[] = new TableReference($this->db, $table, $alias);
        $this->cacheTables[] = $table;
        return $this;
    }

    /**
     * Add a join.
     *
     * @param   string  $toTable        To table.
     * @param   string  $toColumn       To name.
     * @param   string  $fromTable      From table.
     * @param   string  $fromColumn     From name.
     * @param   string  $alias          Alias.
     * @param   string  $comp           Comparison operator.
     * @param   string  $type           Join type.
     * @return  SelectInterface
     */
    public function join(
        string $toTable,
        string $toColumn,
        string $fromTable,
        string $fromColumn,
        ?string $alias = null,
        string $comp = '=',
        string $type = 'left'
    ): SelectInterface 
    {
        $this->joins[] = new Join($this->db, $toTable, $toColumn, $fromTable, $fromColumn, $alias, $comp, $type);
        $this->cacheTables[] = $toTable;
        return $this;
    }

    /**
     * Add a column based select expression.
     *
     * @param   string|array    $cols       Columns.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function col($cols, ?string $alias = null): SelectInterface
    {
        if (is_array($cols)) {
            foreach ($cols as $col => $ali) {
                if (is_int($col)) {
                    $this->selectExprs[] = new SelectExpr($this->db, $ali, null, 'col');
                } else {
                    $this->selectExprs[] = new SelectExpr($this->db, $col, $ali, 'col');
                }
            }
        } else {
            $this->selectExprs[] = new SelectExpr($this->db, $cols, $alias, 'col');
        }
        return $this;
    }

    /**
     * Clear the column selection.
     * 
     * @return  SelectInterface
     */
    public function clearCols(): SelectInterface
    {
        $this->selectExprs = array();
        return $this;
    }

    /**
     * Expr.
     *
     * @param   string          $expr       Expression.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function expr(string $expr, ?string $alias = null): SelectInterface
    {
        $this->selectExprs[] = new SelectExpr($this->db, $expr, $alias, 'expr');
        return $this;
    }

    /**
     * Count aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function count(string $col, ?string $alias = null): SelectInterface
    {
        $this->selectExprs[] = new SelectExpr($this->db, $col, $alias, 'col', 'count');
        return $this;
    }

    /**
     * Year aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function year(string $col, ?string $alias = null): SelectInterface
    {
        $this->selectExprs[] = new SelectExpr($this->db, $col, $alias, 'col', 'year');
        return $this;
    }

    /**
     * Month aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function month(string $col, ?string $alias = null): SelectInterface
    {
        $this->selectExprs[] = new SelectExpr($this->db, $col, $alias, 'col', 'month');
        return $this;
    }

    /**
     * Week aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function week(string $col, ?string $alias = null): SelectInterface
    {
        $this->selectExprs[] = new SelectExpr($this->db, $col, $alias, 'col', 'week');
        return $this;
    }

    /**
     * Get the SQL.
     *
     * @return  string
     * @throws  DbSqlException
     */
    public function getSql() : string
    {
        $ret = 'SELECT ';

        // Expressions.
        if (count($this->selectExprs)) {
            $exprs = array();
            foreach ($this->selectExprs as $expr) {
                $exprs[] = $expr->resolve();
            }
            $ret .= implode(',', $exprs);
        } else {
            $ret .= '*';
        }

        // Froms.
        if (count($this->from)) {
            $froms = array();
            foreach ($this->from as $from) {
                $froms[] = $from->resolve();
            }
            $ret .= ' FROM ' . implode(',', $froms);
        } else {
            throw new DbSqlException("No 'FROM' specfied or SELECT statement");
        }

        // Joins.
        if (count($this->joins)) {
            $joins = array();
            foreach ($this->joins as $join) {
                $joins[] = $join->resolve();
            }
            $ret .= implode('', $joins);
        }

        // Where's
        $ret .= $this->where->resolve();

        // Group.
        $ret .= $this->group->resolve();

        // Having.
        $ret .= $this->having->resolve();

        // Order.
        $ret .= $this->order->resolve();

        // Limit.
        $ret .= $this->limit->resolve();

        // Return
        return $ret;
    }

    /**
     * Prepare.
     *
     * @return  StmtInterface
     */
    public function prepare() : StmtInterface
    {
        $stmt = $this->db->prepare($this->getSql());
        $binds = [];
        if ($stmt) {
            $temp = $this->where->bind($stmt);
            foreach ($temp as $k => $v) {
                $binds[$k] = $v;
            }
            $temp = $this->having->bind($stmt);
            foreach ($temp as $k => $v) {
                $binds[$k] = $v;
            }
        }
        $sql = $this->getSql();
        foreach ($binds as $k => $v) {
            $sql = str_replace($k, $v, $sql);
        }
        $this->cacheSql = $sql;
        return $stmt;
    }

    /**
     * Check the cache.
     * 
     * @param   string      $sql            Sql query to check.
     * @return  mixed
     */
    protected function checkCache(string $sql)
    {
        $md5 = md5($sql);
        $finder = new Finder($this->cachePath, $this->cachePath, new Set(new FileNameStartsWith($md5), Finder::POSITIVE));
        $results = $finder->filter(false);
        if (0 == count($results)) {
            return null;
        }
        logger()->debug(sprintf("Found DB cache for: %s", $md5), null, "DB:CACHE");
        return unserialize(file_get_contents($results[0]));
    }

    /**
     * Save the cache.
     * 
     * @param   string      $sql            Sql query to check.
     * @param   array       $tables         Tables array.
     * @param   mixed       $data           Data to cache.
     * @return
     */
    protected function saveCache(string $sql, array $tables, $data)
    {
        $md5 = md5($sql);
        $fn = $md5 . '.' . implode('.', $tables);
        file_put_contents(Path::join($this->cachePath, $fn . '.cache'), serialize($data));
        logger()->debug(sprintf("Saved DB cache as: %s", $fn), null, "DB:CACHE");
    }

    /**
     * Fetch a column.
     *
     * @param   int         $offset         Offset.
     * @return  mixed
     */
    public function fetchColumn(int $offset = 0)
    {
        return $this->prepare()->fetchColumn($offset);
    }

    /**
     * Fetch an array of data.
     *
     * @return  array
     */
    public function fetchArray() : array
    {
        $start = microtime(true);

        $prepped = $this->prepare();

        // Caching?
        if (!is_null($this->cachePath)) {
            $cached = $this->checkCache($this->cacheSql);
            if (!is_null($cached)) {
                $t = microtime(true) - $start;
                logger()->debug(sprintf("Query (cached) took %f seconds.", $t), null, "DB:CACHE");
                return $cached;
            }
        }

        $result = $prepped->fetchArray();

        // Caching.
        if (!is_null($this->cachePath)) {
            $this->saveCache($this->cacheSql, $this->cacheTables, $result);
        }

        $t = microtime(true) - $start;
        logger()->debug(sprintf("Query (uncached) took %f seconds.", $t), null, "DB:CACHE");
        return $result;
    }

    /**
     * Fetch first record of data.
     *
     * @return  array|null
     */
    public function fetchFirst() : ?array
    {
        $arr = $this->fetchArray();
        if (count($arr) > 0) {
            return $arr[0];
        }
        return null;
    }

    /**
     * Fetch a column of the first record of data.
     *
     * @param   string  $col    Column to fetch.
     * @return  mixed
     */
    public function fetchColumnOfFirstRecord(string $col)
    {
        $data = $this->fetchFirst();
        if (!is_null($data)) {
            if (!array_key_exists($col, $data)) {
                throw new DbSqlException(sprintf("DB table does not have a '%s' column."));
            }
            return $data[$col];
        }
        return null;
    }

    /**
     * Get the cache SQL.
     * 
     * @return  string
     */
    public function getCacheSql(): string
    {
        return $this->cacheSql;
    }

    /**
     * Get the cache tables.
     * 
     * @return  string
     */
    public function getCacheTables(): array
    {
        return $this->cacheTables;
    }
}
