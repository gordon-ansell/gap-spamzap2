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

use GreenFedora\Db\Sql\Part\Where;
use GreenFedora\Db\Sql\Part\WhereClause;
use GreenFedora\Db\DbInterface;

/**
 * SQL having class.
 */
class Having extends Where
{
    /**
     * Add a having clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @param   string  $join       Join.
     * @return  void
     */
    public function having(string $column, $values = null, string $comp = '=', string $join = 'and')
    {
        $this->clauses[] = array($join, new WhereClause($this->db, $column, $values, $comp));
    }

    /**
     * Get the where SQL.
     *
     * @return string
     */
    public function resolve() : string
    {
        return str_replace(' WHERE ', ' HAVING ', parent::resolve());
    }
}
