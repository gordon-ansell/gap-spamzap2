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

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\DbInterface;

use GreenFedora\Db\Sql\Part\ColumnName;
use GreenFedora\Db\Sql\Part\TableName;
use GreenFedora\Db\Sql\Part\TableReference;

/**
 * SQL where clause class.
 */
class Join extends AbstractSql
{
    /**
     * From table.
     * @var TableName
     */
    protected $fromTable = null;

    /**
     * To table.
     * @var TableReference
     */
    protected $toTable = null;

    /**
     * From column.
     * @var ColumnName
     */
    protected $fromColumn = null;

    /**
     * To column.
     * @var ColumnName
     */
    protected $toColumn = null;

    /**
     * Comparison operator.
     * @var string
     */
    protected $comp = '=';

    /**
     * Type.
     * @var string
     */
    protected $type = 'left';

    /**
     * Constructor.
     *
     * @param   DbInterface      $db             Database parent.
     * @param   string  $toTable        To table.
     * @param   string  $toColumn       To name.
     * @param   string  $fromTable      From table.
     * @param   string  $fromColumn     From name.
     * @param   string  $alias          Alias.
     * @param   string  $comp           Comparison operator.
     * @param   string  $type           Join type.
     * @return  void
     */
    public function __construct(
        DbInterface $db,
        string $toTable,
        string $toColumn,
        string $fromTable,
        string $fromColumn,
        ?string $alias = null,
        string $comp = '=',
        string $type = 'left'
    ) {
    
        parent::__construct($db);
        $this->fromTable = new TableName($db, $fromTable);
        $this->fromColumn = new ColumnName($db, $fromColumn);
        $this->toTable = new TableReference($db, $toTable, $alias);
        $this->toColumn = new ColumnName($db, $toColumn);
        $this->comp = $comp;
        $this->type = $type;
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        $ret = ' ' . strtoupper($this->type) . ' JOIN ';
        $ret .= $this->toTable->resolve() . ' ON ';
        $ret .= $this->db->idq($this->fromTable->getName() . '.' . $this->fromColumn->getName());
        $ret .= ' ' . $this->comp . ' ';
        $ret .= $this->db->idq($this->toTable->getAlias() . '.' . $this->toColumn->getName());
        return $ret;
    }

    /**
     * Debugging.
     * 
     * @return array
     */
    public function __debugInfo()
    {
        return array('fromTable' => $this->fromTable, 'fromColumn' => $this->fromColumn, 'toTable' => $this->toTable,
            'toColumn' => $this->toColumn, 'comp' => $this->comp, 'type' => $this->type);
    }
}
