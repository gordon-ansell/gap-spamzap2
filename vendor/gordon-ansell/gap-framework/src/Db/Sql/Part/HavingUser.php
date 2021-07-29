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
namespace GreenFedora\Db\Sql\Part;

use GreenFedora\Db\Driver\Stmt\Stmt;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\Sql\Part\WhereClause;
use GreenFedora\Db\DbInterface;

/**
 * SQL having user trait.
 */
trait HavingUser
{
    /**
     * Having.
     * @var Having
     */
    protected $having = null;

    /**
     * Add a having clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  self
     */
    public function having(string $column, $values = null, string $comp = '=', string $join = 'and')
    {
        $this->having->having($column, $values, $comp, $join);
        return $this;
    }

    /**
     * Add an open bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function havingOpen(string $join = 'and')
    {
        $this->having->open($join);
        return $this;
    }

    /**
     * Add a close bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function havingClose(string $join = 'and')
    {
        $this->having->close($join);
        return $this;
    }
}
