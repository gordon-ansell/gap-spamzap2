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
use GreenFedora\Db\Sql\Part\ValueCollection;

/**
 * SQL where clause class.
 */
class WhereClause extends AbstractSql
{
    /**
     * Column name.
     * @var ColumnName
     */
    protected $column = null;

    /**
     * Values.
     * @var ValueCollection
     */
    protected $values = null;

    /**
     * Comparison operator.
     * @var string
     */
    protected $comp = '=';

    /**
     * Is an open?
     * @var bool
     */
    protected $open = false;

    /**
     * Is a close?
     * @var bool
     */
    protected $close = false;

    /**
     * Constructor.
     *
     * @param   DbInterface      $db         Database parent.
     * @param   string  $column     Column name.
     * @param   mixed   $values     Value.
     * @param   string  $comp       Comparison operator.
     * @return  void
     */
    public function __construct(DbInterface $db, string $column, $values = null, string $comp = '=')
    {
        parent::__construct($db);
        if ('(' == $column) {
            $this->open = true;
        } else if (')' == $column) {
            $this->close = true;
        } else if (is_null($values)) {
            $this->column = new ColumnName($db, $column);
            $this->values = null;
            $this->comp = $comp;
        } else {
            $this->column = new ColumnName($db, $column);
            $this->values = new ValueCollection($values);
            $this->comp = $comp;
        }
    }

    /**
     * Bind values.
     *
     * @param   StmtInterface    $stmt       Statement.
     * @return  array                        Bound values.
     */
    public function bind(StmtInterface $stmt): array
    {
        if (!$this->open and !$this->close and !is_null($this->values)) {
            $ret = $this->values->bind($stmt);
            return $ret;
        }
        return [];
    }

    /**
     * Is this an open?
     *
     * @return  bool
     */
    public function isOpen() : bool
    {
        return $this->open;
    }

    /**
     * Is this a close?
     *
     * @return  bool
     */
    public function isClose() : bool
    {
        return $this->close;
    }

    /**
     * Is open or close.
     *
     * @return  bool
     */
    public function isOpenOrClose() : bool
    {
        return $this->isOpen() or $this->isClose();
    }

    /**
     * Resolve this clause.
     *
     * @return string
     */
    public function resolve() : string
    {
        if ($this->open) {
            return '(';
        } else if ($this->close) {
            return ')';
        }

        $ret = $this->column->resolve();
        
        if ('like' == $this->comp) {
            $ret .= ' LIKE ' . $this->values->getValue(0)->resolve();            
        } else {

            if (!is_null($this->values) and $this->values->count() > 1) {
                if ('!=' == $this->comp) {
                    $ret .= ' NOT IN ';
                } else {
                    $ret .= ' IN ';
                }
                $ret .= $this->values->resolve();
            } else {
                if (is_null($this->values)) {
                    if ('!=' == $this->comp) {
                        $ret .= ' IS NOT NULL';
                    } else {
                        $ret .= ' IS NULL';
                    }
                } else {
                    $ret .= ' ' . $this->comp . ' ' . $this->values->getValue(0)->resolve();
                }
            }
        }

        return $ret;
    }
}
