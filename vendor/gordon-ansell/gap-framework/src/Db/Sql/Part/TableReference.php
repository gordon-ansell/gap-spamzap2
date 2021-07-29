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

use GreenFedora\Db\Sql\Part\TableName;

/**
 * SQL table name class.
 */
class TableReference extends AbstractSql
{
    /**
     * Table name.
     * @var TableName
     */
    protected $table = null;

    /**
     * Alias.
     * @var string|null
     */
    protected $alias = null;

    /**
     * Constructor.
     *
     * @param   DbInterface          $db         Parent database.
     * @param   string      $table      Table name.
     * @param   string|null $alias      Alias.
     * @return  void
     */
    public function __construct(DbInterface $db, string $table, ?string $alias = null)
    {
        parent::__construct($db);
        $this->table = new TableName($db, $table);
        $this->alias = $alias;
    }

    /**
     * Get the alias.
     *
     * @return  string
     */
    public function getAlias() : ?string
    {
        if (null === $this->alias) {
            return $this->table->getName();
        } else {
            return $this->alias;
        }
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        $ret = $this->table->resolve();
        if (null !== $this->alias) {
            $ret .= ' AS ' . $this->db->idq($this->alias);
        }
        return $ret;
    }
}
