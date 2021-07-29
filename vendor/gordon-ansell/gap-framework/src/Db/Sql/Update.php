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
use GreenFedora\Db\Sql\UpdateInterface;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\Part\TableName;
use GreenFedora\Db\Sql\Part\ColumnNameCollection;
use GreenFedora\Db\Sql\Part\ValueCollection;
use GreenFedora\Db\Sql\Part\WhereUser;
use GreenFedora\Db\Sql\Part\Where;
use GreenFedora\Db\Sql\Part\LimitUser;
use GreenFedora\Db\Sql\Part\Limit;

use GreenFedora\Db\DbInterface;

/**
 * SQL update class.
 */
class Update extends AbstractSql implements UpdateInterface
{
    use WhereUser;
    use LimitUser;

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
     * @var ValueCollection
     */
    protected $values = null;

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
        $this->values = new ValueCollection();
        $this->where = new Where($db);
        $this->limit = new Limit();

        foreach ($colVals as $col => $val) {
            $this->columns->addColumn($col);
            $this->values->addValue($val);
        }
    }

    /**
     * Get the SQL.
     *
     * @return  string
     */
    public function getSql() : string
    {
        $ret = 'UPDATE ' . $this->table->resolve() . ' SET ';
        $sets = array();
        $count = 0;
        for ($count = 0; $count < $this->columns->count(); $count++) {
            $tmp = $this->columns->getColumn($count)->resolve() . ' = ';
            $tmp .= $this->values->getValue($count)->resolve();
            $sets[] = $tmp;
        }
        $ret .= implode(',', $sets);
        return $ret . $this->where->resolve() . $this->limit->resolve();
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
            $this->values->bind($stmt);
            $this->where->bind($stmt);
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
