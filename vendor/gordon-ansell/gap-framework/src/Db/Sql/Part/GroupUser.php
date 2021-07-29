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
use GreenFedora\Db\Sql\Part\Group;

/**
 * SQL Group trait.
 */
trait GroupUser
{

    /**
     * Group.
     * @var Group
     */
    protected $group = null;

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
        $this->group->group($column, $expr, $rollUp);
        return $this;
    }

    /**
     * Set the rollup.
     *
     * @param   bool        $rollUp     Flag.
     * @return  void
     */
    public function rollUp(bool $rollUp = true)
    {
        $this->group->rollUp($rollUp);
        return $this;
    }
}
