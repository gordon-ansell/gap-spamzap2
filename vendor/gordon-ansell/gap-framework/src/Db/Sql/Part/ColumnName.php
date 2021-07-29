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

/**
 * SQL column name class.
 */
class ColumnName extends AbstractSql
{
    /**
     * Column name.
     * @var string
     */
    protected $column = null;

    /**
     * Constructor.
     *
     * @param   DbInterface          $db         Parent database.
     * @param   string      $column     Column name.
     * @return  void
     */
    public function __construct(DbInterface $db, string $column)
    {
        parent::__construct($db);
        $this->column = $column;
    }

    /**
     * Get the column name.
     *
     * @return  string
     */
    public function getName() : string
    {
        return $this->column;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        return $this->db->idq($this->column);
    }
}
