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

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\DbInterface;

use GreenFedora\db\Sql\Select;
use GreenFedora\db\Sql\SelectInterface;

/**
 * SQL select expression class.
 */
class SelectExpr extends AbstractSql
{
    /**
     * Expr.
     * @var mixed
     */
    protected $expr = null;

    /**
     * Alias.
     * @var mixed
     */
    protected $alias = null;

    /**
     * Expression type.
     * @var string
     */
    protected $type = null;

    /**
     * Aggregate.
     * @var string
     */
    protected $aggr = null;

    /**
     * Constructor.
     *
     * @param   DbInterface          $db         Parent database.
     * @param   mixed       $expr       Expression.
     * @param   string|null $alias      Alias.
     * @param   string|null $type       Expression type.
     * @return  void
     */
    public function __construct(DbInterface $db, $expr, ?string $alias = null, ?string $type = null, ?string $aggr = null)
    {
        parent::__construct($db);
        $this->expr = $expr;
        $this->alias = $alias;
        $this->aggr = $aggr;

        if (null === $type) {
            if ($expr instanceof SelectInterface) {
                $type = 'select';
            } else if (is_string($expr)) {
                if (preg_match("/[A-Za-z0-9\.\*]+/", $expr)) {
                    $type = 'col';
                } else {
                    $type = 'expr';
                }
            } else {
                $type = 'expr';
            }
        }
        $this->type = $type;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        if ('expr' == $this->type) {
            $ret = '(' . $this->expr . ')';
        } else if ('select' == $this->expr) {
            $ret = '(' . $this->expr->getSql() . ')';
        } else {
            if (null === $this->aggr) {
                $ret = $this->db->idq($this->expr);
            } else {
                $ret = strtoupper($this->aggr) . '(' . $this->db->idq($this->expr) . ')';
            }
        }
        if (null !== $this->alias) {
            $ret .= ' AS ' . $this->alias;
        }
        return $ret;
    }

    /**
     * Debugging.
     * 
     * @return array
     */
    public function __debugInfo() {
        return array('expr' => $this->expr, 'alias' => $this->alias, 'type' => $this->type, 'aggr' => $this->aggr);
    }
}
