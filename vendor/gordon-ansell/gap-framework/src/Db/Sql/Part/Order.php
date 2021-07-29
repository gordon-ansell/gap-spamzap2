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

use GreenFedora\Db\Sql\Part\ColumnNameCollection;

use GreenFedora\Db\DbInterface;
use GreenFedora\Db\Sql\AbstractSql;

/**
 * SQL Order class.
 */
class Order extends AbstractSql
{
    /**
     * Columns.
     * @var ColumnNameCollection
     */
    protected $columns = null;

    /**
     * Expressions.
     * @var array
     */
    protected $expr = array();

    /**
     * Constructor.
     *
     * @param   DbInterface         $db          DB handle.
     * @param   string     $column      Column name.
     * @param   string     $expr        Expression.
     * @return  void
     */
    public function __construct(DbInterface $db, ?string $column = null, string $expr = 'asc')
    {
        parent::__construct($db);
        $this->columns = new ColumnNameCollection($db);
        if (null !== $column) {
            $this->order($column, $expr);
        }
    }

    /**
     * Set an order by.
     *
     * @param   string     $column      Column name.
     * @param   string     $expr        Expression.
     * @return  void
     */
    public function order(string $column, string $expr = 'asc')
    {
        $this->columns->addColumn($column);
        $this->expr[] = $expr;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        $ret = '';
        $count = 0;
        foreach ($this->expr as $expr) {
            if ($ret != '') {
                $ret .= ',';
            }
            $colName = $this->columns->getColumn($count)->resolve();
            $ret .= $colName . ' ' . strtoupper($expr);
            $count++;
        }
        if ('' != $ret) {
            return ' ORDER BY ' . $ret;
        } else {
            return $ret;
        }
    }
}
