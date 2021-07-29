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

use GreenFedora\Db\Sql\SelectInterface;

/**
 * SQL Limit trait.
 */
interface LimitUserInterface
{
    /**
     * Set the limit and offset.
     *
     * @param   int     $limit      Row count.
     * @param   int     $offset     Offset.
     * @return  void
     */
    public function limit(int $limit = -1, int $offset = -1): SelectInterface;

    /**
     * Set the offset.
     *
     * @param   int     $offset     Offset.
     * @return  void
     */
    public function offset(int $offset): SelectInterface;
}
