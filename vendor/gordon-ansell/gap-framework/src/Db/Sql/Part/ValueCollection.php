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
use GreenFedora\Db\Sql\Part\Value;
use GreenFedora\db\Driver\Stmt\StmtInterface;

/**
 * SQL value collection class.
 */
class ValueCollection
{
    /**
     * Values.
     * @var Value[]
     */
    protected $values = array();

    /**
     * Constructor.
     *
     * @param   mixed       $values         Values to add to the collection.
     * @return  void
     */
    public function __construct($values = array())
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!empty($values)) {
            foreach ($values as $val) {
                $this->addValue($val);
            }
        }
    }

    /**
     * Count the values.
     *
     * @return  int
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * Get a value by its index number.
     *
     * @param   int         $index      Index number.
     * @return  Value
     * @throws  Exception\OutOfBoundsException
     */
    public function getValue(int $index) : Value
    {
        if (($index < 0) or ($index >= count($this->values))) {
            throw new Exception\OutOfBoundsException(sprintf("No value exists at index %s", $index));
        }
        return $this->values[$index];
    }

    /**
     * Add a value.
     *
     * @param   mixed       $value      Value.
     * @return  void
     */
    public function addValue($value)
    {
        $this->values[] = new Value($value);
    }

    /**
     * Bind all values.
     *
     * @param   StmtInterface        $stmt       Statement.
     * @return  array                            Array of bound values.            
     */
    public function bind(StmtInterface $stmt): array
    {
        $ret = [];
        foreach ($this->values as $value) {
            list($k, $v) = $value->bind($stmt);
            $ret[$k] = $v;
        }
        return $ret;
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
        foreach ($this->values as $value) {
            if (!$first) {
                $ret .= $sep;
            } else {
                $first = false;
            }
            $ret .= $value->resolve();
        }
        $ret .= $close;
        return $ret;
    }
}
