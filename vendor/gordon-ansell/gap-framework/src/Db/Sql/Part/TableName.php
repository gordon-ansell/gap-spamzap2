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
 * SQL table name class.
 */
class TableName extends AbstractSql
{
    /**
     * Table name.
     * @var string
     */
    protected $table = null;

    /**
     * Constructor.
     *
     * @param   DbInterface          $db         Parent database.
     * @param   string      $table      Table name.
     * @return  void
     */
    public function __construct(DbInterface $db, string $table)
    {
        parent::__construct($db);
        $this->table = $table;
    }

    /**
     * Get the table name.
     *
     * @return  string
     */
    public function getName() : string
    {
        return $this->table;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        return $this->db->tn($this->table);
    }
}
