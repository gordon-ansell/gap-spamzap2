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
interface WhereUserInterface
{
    /**
     * Add a where clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  static
     */
    public function where(string $column, $values = null, string $comp = '=', string $join = 'and');

    /**
     * Add a where like clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $value      Value.
     * @return  static
     */
    public function whereLike(string $column, $value = null, string $join = 'and');

    /**
     * Add a where not-equals clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  static
     */
    public function whereNe(string $column, $values = null, string $join = 'and');

    /**
     * Add an OR where clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @return  static
     */
    public function orWhere(string $column, $values = null, string $comp = '=');

    /**
     * Add an open bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function whereOpen(string $join = 'and');

    /**
     * Add a close bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function whereClose(string $join = 'and');
}
