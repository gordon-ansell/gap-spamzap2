<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
declare(strict_types=1);
namespace GreenFedora\Db\Sql;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\Sql\InsertInterface;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\Part\TableName;
use GreenFedora\Db\Sql\Part\ColumnNameCollection;
use GreenFedora\Db\Sql\Part\ValueCollection;

use GreenFedora\Db\DbInterface;

/**
 * SQL insert class.
 */
class Insert extends AbstractSql implements InsertInterface
{
    /**
     * Table name.
     * @var TableName
     */
    protected $table = null;

    /**
     * Columns.
     * @var ColumnNameCollection
     */
    protected $columns = null;

    /**
     * Values.
     * @var ValueCollection[]
     */
    protected $values = array();

    /**
     * Constructor.
     *
     * @param   DbInterface      $db         Database parent.
     * @param   string           $table      Table name.
     * @param   array            $colVals    Columns and values.
     * @return  void
     */
    public function __construct(DbInterface $db, string $table, array $colVals)
    {
        parent::__construct($db);
        $this->table = new TableName($db, $table);
        $this->columns = new ColumnNameCollection($db);

        if (isset($colVals[0]) and is_array($colVals[0])) {
            foreach ($colVals[0] as $col => $val) {
                $this->columns->addColumn($col);
            }
            foreach ($colVals as $val) {
                $this->values[] = new ValueCollection(array_values($val));
            }
        } else {
            $vc = new ValueCollection();
            foreach ($colVals as $col => $val) {
                $this->columns->addColumn($col);
                $vc->addValue($val);
            }
            $this->values[] = $vc;
        }
    }

    /**
     * Get the SQL.
     *
     * @return  string
     */
    public function getSql() : string
    {
        $ret = 'INSERT INTO ' . $this->table->resolve();
        $ret .= ' ' . $this->columns->resolve();
        $ret .= ' VALUES ';
        $vals = '';
        foreach ($this->values as $val) {
            if ('' != $vals) {
                $vals .= ',';
            }
            $vals .= $val->resolve();
        }
        $ret .= $vals;
        return $ret;
    }

    /**
     * Prepare.
     * 
     * @return  StmtInterface
     */
    public function prepare() : ?StmtInterface
    {
        $stmt = $this->db->prepare($this->getSql());
        if ($stmt) {
            foreach ($this->values as $vc) {
                $vc->bind($stmt);
            }
            return $stmt;
        }
        return null;
    }

    /**
     * Execute.
     *
     * @return  bool
     */
    public function execute()
    {
        $stmt = $this->prepare();
        if (!is_null($this->cachePath)) {
            $this->clearCache($this->table->getName());
        }
        if (null !== $stmt) {
            return $stmt->execute();
        }
        return false;
    }

}
