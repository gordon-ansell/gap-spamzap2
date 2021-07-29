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

use GreenFedora\Db\DbInterface;
use GreenFedora\Db\Sql\Part\Order;

/**
 * SQL Group class.
 */
class Group extends Order
{
    /**
     * Rollup?
     * @var bool
     */
    protected $rollUp = false;

    /**
     * Constructor.
     *
     * @param   DbInterface         $db          DB handle.
     * @param   string     $column      Column name.
     * @param   string     $expr        Expression.
     * @param   bool       $rollUp      Roll up?
     * @return  void
     */
    public function __construct(DbInterface $db, ?string $column = null, string $expr = 'asc', bool $rollUp = false)
    {
        parent::__construct($db, $column, $expr);
        $this->rollUp = $rollUp;
    }

    /**
     * Set an order by.
     *
     * @param   string     $column      Column name.
     * @param   string     $expr        Expression.
     * @param   bool       $rollUp      Roll up?
     * @return  void
     */
    public function group(string $column, string $expr = 'asc', bool $rollUp = false)
    {
        $this->order($column, $expr);
        $this->rollUp = $rollUp;
    }

    /**
     * Set the rollup.
     *
     * @param   bool        $rollUp     Flag.
     * @return  void
     */
    public function rollUp(bool $rollUp = true)
    {
        $this->rollUp = $rollUp;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        $ret = parent::resolve();
        if ('' != $ret) {
            $ret = str_replace(' ORDER BY ', ' GROUP BY ', $ret);
        } else {
            return $ret;
        }
        if ($this->rollUp) {
            $ret .= ' WITH ROLLUP';
        }
        return $ret;
    }
}
