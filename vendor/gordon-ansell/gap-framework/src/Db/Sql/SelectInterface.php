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
use GreenFedora\Db\Driver\Stmt\StmtInterface;
use GreenFedora\Db\Sql\Part\LimitUserInterface;
use GreenFedora\Db\Sql\Part\WhereUserInterface;
use GreenFedora\Db\Sql\Part\OrderUserInterface;

/**
 * SQL select classninterface.
 */
interface SelectInterface extends WhereUserInterface, OrderUserInterface, LimitUserInterface
{
    /**
     * Add a from.
     *
     * @param   string      $table      Table.
     * @param   string|null $alias      Table alias.
     * @return  SelectInterface
     */
    public function from(string $table, ?string $alias = null): SelectInterface;

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
    ): SelectInterface;

    /**
     * Add a column based select expression.
     *
     * @param   string|array    $cols       Columns.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function col($cols, ?string $alias = null): SelectInterface;

    /**
     * Clear the column selection.
     * 
     * @return  SelectInterface
     */
    public function clearCols(): SelectInterface;

    /**
     * Expr.
     *
     * @param   string          $expr       Expression.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function expr(string $expr, ?string $alias = null): SelectInterface;

    /**
     * Count aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function count(string $col, ?string $alias = null): SelectInterface;

    /**
     * Year aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function year(string $col, ?string $alias = null): SelectInterface;

    /**
     * Month aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function month(string $col, ?string $alias = null): SelectInterface;

    /**
     * Week aggregate.
     *
     * @param   string          $col        Column.
     * @param   string|null     $alias      Alias.
     * @return  SelectInterface
     */
    public function week(string $col, ?string $alias = null): SelectInterface;

    /**
     * Get the SQL.
     *
     * @return  string
     * @throws  DbSqlException
     */
    public function getSql() : string;

    /**
     * Prepare.
     *
     * @return  StmtInterface
     */
    public function prepare() : StmtInterface;

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
     * Fetch first record of data.
     *
     * @return  array|null
     */
    public function fetchFirst() : ?array;

    /**
     * Fetch a column of the first record of data.
     *
     * @param   string  $col    Column to fetch.
     * @return  mixed
     */
    public function fetchColumnOfFirstRecord(string $col);

    /**
     * Get the cache SQL.
     * 
     * @return  string
     */
    public function getCacheSql(): string;

    /**
     * Get the cache tables.
     * 
     * @return  string
     */
    public function getCacheTables(): array;
}
