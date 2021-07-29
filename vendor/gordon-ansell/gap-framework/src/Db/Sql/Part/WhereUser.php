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

/**
 * SQL where user trait.
 */
trait WhereUser
{
    /**
     * Where.
     * @var Where
     */
    protected $where = null;

    /**
     * Add a where clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  self
     */
    public function where(string $column, $values = null, string $comp = '=', string $join = 'and')
    {
        $this->where->where($column, $values, $comp, $join);
        return $this;
    }

    /**
     * Add a where like clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $value      Value.
     * @return  self
     */
    public function whereLike(string $column, $value = null, string $join = 'and')
    {
        $this->where->where($column, $value, 'like', $join);
        return $this;
    }

    /**
     * Add a where not-equals clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  self
     */
    public function whereNe(string $column, $values = null, string $join = 'and')
    {
        $this->where->where($column, $values, '!=', $join);
        return $this;
    }

    /**
     * Add an OR where clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  self
     */
    public function orWhere(string $column, $values = null, string $comp = '=')
    {
        $this->where->where($column, $values, $comp, 'or');
        return $this;
    }

    /**
     * Add an open bracket.
     *
     * @param   string  $join       Join.
     * @return  self
     */
    public function whereOpen(string $join = 'and')
    {
        $this->where->open($join);
        return $this;
    }

    /**
     * Add a close bracket.
     *
     * @param   string  $join       Join.
     * @return  self
     */
    public function whereClose(string $join = 'and')
    {
        $this->where->close($join);
        return $this;
    }
}
