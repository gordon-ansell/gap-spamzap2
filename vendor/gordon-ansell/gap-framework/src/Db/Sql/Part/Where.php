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

use GreenFedora\Db\Sql\Part\Exception\DbSqlPartException;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\Sql\Part\WhereClause;
use GreenFedora\Db\DbInterface;

/**
 * SQL where class.
 */
class Where extends AbstractSql
{
    /**
     * Where clauses.
     * @var array
     */
    protected $clauses = array();

    /**
     * Constructor.
     *
     * @param   DbInterface      $db         Database parent.
     * @return  void
     */
    public function __construct(DbInterface $db)
    {
        parent::__construct($db);
    }

    /**
     * Bind values.
     *
     * @param   StmtInterface    $stmt       Statement.
     * @return  array                        Bound values.
     */
    public function bind(StmtInterface $stmt): array
    {
        $ret = [];
        foreach ($this->clauses as $clause) {
            $temp = $clause[1]->bind($stmt);
            foreach ($temp as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    /**
     * Add a where clause.
     *
     * @param   string  $column     Column.
     * @param   mixed   $values     Values.
     * @param   string  $comp       Comparison operator.
     * @param   string  $join       Join.
     * @return  void
     */
    public function where(string $column, $values = null, string $comp = '=', string $join = 'and')
    {
        $this->clauses[] = array($join, new WhereClause($this->db, $column, $values, $comp));
    }

    /**
     * Add an open bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function open(string $join = 'and')
    {
        $this->clauses[] = array($join, new WhereClause($this->db, '('));
    }

    /**
     * Add a close bracket.
     *
     * @param   string  $join       Join.
     * @return  void
     */
    public function close(string $join = 'and')
    {
        $this->clauses[] = array($join, new WhereClause($this->db, ')'));
    }

    /**
     * Get the where SQL.
     *
     * @return string
     */
    public function resolve() : string
    {
        if (0 == count($this->clauses)) {
            return '';
        }

        $ret = '';
        $count = 0;
        foreach ($this->clauses as $clause) {
            $wantJoin = true;
            if ('' == $ret) {
                $wantJoin = false;
            }
            if (($count > 0) and ($this->clauses[$count - 1][1]->isOpen())) {
                $wantJoin = false;
            }
            if ($clause[1]->isClose()) {
                $wantJoin = false;
            }

            if ($wantJoin) {
                $ret .= ' ' . strtoupper($clause[0]) . ' ';
            }
            if (!$clause[1] instanceof WhereClause) {
                throw new DbSqlPartException(sprintf("Where clause is not of type 'WhereClause', it's a %s", gettype($clause[1])));
            }
            $ret .= $clause[1]->resolve();
            $count++;
        }
        return ' WHERE ' . $ret;
    }
}
