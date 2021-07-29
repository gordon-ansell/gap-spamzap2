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
use GreenFedora\Db\Sql\Part\ColumnName;

/**
 * SQL column name collection class.
 */
class ColumnNameCollection extends AbstractSql
{
    /**
     * Column name.
     * @var ColumnName[]
     */
    protected $columns = array();

    /**
     * Constructor.
     *
     * @param   DbInterface          $db         Parent database.
     * @return  void
     */
    public function __construct(DbInterface $db)
    {
        parent::__construct($db);
    }

    /**
     * Add a column.
     *
     * @param   string      $column     Column name.
     * @return  void
     */
    public function addColumn(string $column)
    {
        $this->columns[] = new ColumnName($this->db, $column);
    }

    /**
     * Count the values.
     *
     * @return  int
     */
    public function count() : int
    {
        return count($this->columns);
    }

    /**
     * Get a column by its index number.
     *
     * @param   int         $index      Index number.
     * @return  ColumnName
     * @throws  Exception\OutOfBoundsException
     */
    public function getColumn(int $index) : ColumnName
    {
        if (($index < 0) or ($index >= count($this->columns))) {
            throw new Exception\OutOfBoundsException(sprintf("No column exists at index %s", $index));
        }
        return $this->columns[$index];
    }

    /**
     * Resolve this clause.
     *
     * @param   string      $open       Opening thingy.
     * @param   string      $close      Closing thingy.
     * @param   string      $sep        Separator.
     * @return  string
     */
    public function resolve(string $open = '(', string $close = ')', string $sep = ',') : string
    {
        $ret = $open;
        $first = true;
        foreach ($this->columns as $column) {
            if (!$first) {
                $ret .= $sep;
            } else {
                $first = false;
            }
            $ret .= $column->resolve();
        }
        $ret .= $close;
        return $ret;
    }
}
