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

use GreenFedora\Db\Sql\Part\Order;
use GreenFedora\Db\Sql\SelectInterface;

/**
 * SQL Order trait.
 */
trait OrderUser
{
    /**
     * Order.
     * @var Order
     */
    protected $order = null;

    /**
     * Set an order by.
     *
     * @param   string     $column      Column name.
     * @param   string     $expr        Expression.
     * @return  static
     */
    public function order(string $column, string $expr = 'asc'): SelectInterface
    {
        $this->order->order($column, $expr);
        return $this;
    }
}
