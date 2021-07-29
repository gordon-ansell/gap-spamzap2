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
use GreenFedora\Db\Sql\DeleteInterface;

use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\Part\TableName;
use GreenFedora\Db\Sql\Part\WhereUser;
use GreenFedora\Db\Sql\Part\Where;
use GreenFedora\Db\Sql\Part\LimitUser;
use GreenFedora\Db\Sql\Part\Limit;

use GreenFedora\Db\Dbinterface;

/**
 * SQL update class.
 */
class Delete extends AbstractSql implements DeleteInterface
{
    use WhereUser;
    use LimitUser;

    /**
     * Table name.
     * @var TableName
     */
    protected $table = null;

    /**
     * Constructor.
     *
     * @param   DbInterface     $db         Database parent.
     * @param   string          $table      Table name.
     * @param   array           $wheres     Columns and values for wheres.
     * @return  void
     */
    public function __construct(DbInterface $db, string $table, ?array $wheres = null)
    {
        parent::__construct($db);
        $this->table = new TableName($db, $table);
        $this->where = new Where($db);
        $this->limit = new Limit();

        if (null !== $wheres) {
            foreach ($wheres as $col => $val) {
                $this->where($col, $val);
            }
        }
    }

    /**
     * Get the SQL.
     *
     * @return  string
     */
    public function getSql() : string
    {
        $ret = 'DELETE FROM ' . $this->table->resolve();
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
