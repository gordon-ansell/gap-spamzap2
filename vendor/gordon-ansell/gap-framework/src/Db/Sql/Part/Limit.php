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

/**
 * SQL Limit class.
 */
class Limit
{
    /**
     * Limit.
     * @var int
     */
    protected $limit = -1;

    /**
     * Offset.
     * @var
     */
    protected $offset = -1;

    /**
     * Constructor.
     *
     * @param   int     $limit      Row count.
     * @param   int     $offset     Offset.
     * @return  void
     */
    public function __construct(int $limit = -1, int $offset = -1)
    {
        $this->limit($limit, $offset);
    }

    /**
     * Set the limit and offset.
     *
     * @param   int     $limit      Row count.
     * @param   int     $offset     Offset.
     * @return  void
     */
    public function limit(int $limit = -1, int $offset = -1)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * Set the offset.
     *
     * @param   int     $offset     Offset.
     * @return  void
     */
    public function offset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        if ((-1 == $this->limit) and (-1 == $this->offset)) {
            return '';
        } else if (-1 != $this->limit) {
            if (-1 == $this->offset) {
                return ' LIMIT ' . $this->limit;
            } else {
                return ' LIMIT ' . $this->offset . ',' . $this->limit;
            }
        }
    }
}
