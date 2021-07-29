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
namespace GreenFedora\Db\Schema;

use GreenFedora\Db\DbInterface;

use GreenFedora\Db\Schema\TableInterface;
use GreenFedora\Db\Schema\SchemaInterface;
use GreenFedora\Db\Schema\ColInterface;
use GreenFedora\Db\Schema\Col;

use GreenFedora\Db\Schema\Col\ColPrimaryAuto;
use GreenFedora\Db\Schema\Col\ColVarChar;
use GreenFedora\Db\Schema\Col\ColChar;
use GreenFedora\Db\Schema\Col\ColVarCharPrimary;
use GreenFedora\Db\Schema\Col\ColDateTime;
use GreenFedora\Db\Schema\Col\ColTinyInt;
use GreenFedora\Db\Schema\Col\ColSmallInt;
use GreenFedora\Db\Schema\Col\ColInt;
use GreenFedora\Db\Schema\Col\ColBigInt;
use GreenFedora\Db\Schema\Col\ColDecimal;
use GreenFedora\Db\Schema\Col\ColText;

use GreenFedora\Db\Schema\Exception\InvalidArgumentException;

/**
 * Database schema table.
 */
class Table implements TableInterface
{
    /**
     * Parent schema.
     * @var Schema
     */
    protected $schema = null;

    /**
     * Database entry point.
     * @var DbInterface
     */
    protected $db = null;

    /**
     * Table name.
     * @var string
     */
    protected $name = '';

    /**
     * Columns.
     * @var ColInterface[]
     */
    protected $columns = array();

    /**
     * Properties.
     * @var array
     */
    protected $props = array();

    /**
     * Constructor.
     *
     * @param   DbInterface         $db         Database entry point.
     * @param   SchemaInterface     $schema     Parent schema.
     * @param   string              $name       Table name.
     * @param   array               $props      Table properties.
     * @return  void
     */
    public function __construct(DbInterface $db, SchemaInterface $schema, string $name, array $props = array())
    {
        $this->schema = $schema;
        $this->db = $db;
        $this->name = $name;
        $this->props = $props;

        $this->init();
    }

    /**
     * Initialise.
     *
     * @return  void
     */
    protected function init()
    {
    }

    /**
     * Get the name.
     *
     * @return  string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the parent schema.
     *
     * @return  SchemaInterface
     */
    public function getSchema() : SchemaInterface
    {
        return $this->schema;
    }

    /**
     * Get a column.
     *
     * @param   string          $name       Column name.
     * @return  ColInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn(string $name) : ColInterface
    {
        if (array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        }
        throw new InvalidArgumentException(sprintf("Column '%s' does not exist", $name));
    }

    /**
     * Add a column to this table.
     *
     * @param   string|Col      $name       Column name.
     * @param   string|null     $type       Column type.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumn($name, ?string $type = null, array $props = array()) : TableInterface
    {
        if ($name instanceof Col) {
            $this->columns[$name->getName()] = $name;
        } else {
            $this->columns[$name] = new Col($this->db, $this, $name, $type, $props);
        }
        return $this;
    }

    /**
     * Add a primary auto column to this table.
     *
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnPrimaryAuto(string $name = 'id', array $props = array()) : TableInterface
    {
        $name = $this->name . '_' . $name;
        $this->columns[$name] = new ColPrimaryAuto($this->db, $this, $name, $props);
        return $this;
    }

    /**
     * Add a varchar column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnVarChar(string $name, int $length = 255, ?string $default = '', 
        array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColVarChar($this->db, $this, $name, $length, $default, $props);
        return $this;
    }

    /**
     * Add a char column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnChar(string $name, int $length = 255, ?string $default = '', 
        array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColChar($this->db, $this, $name, $length, $default, $props);
        return $this;
    }

    /**
     * Add a primary varchar column to this table.
     *
     * @param   int             $length     Length.
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnVarCharPrimary(int $length, string $name = 'id', array $props = array()) : TableInterface
    {
        $name = $this->name . '_' . $name;
        $this->columns[$name] = new ColVarCharPrimary($this->db, $this, $name, $length, $props);
        return $this;
    }

    /**
     * Add a tinyint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnTinyInt(string $name, int $default = 0, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColTinyInt($this->db, $this, $name, $default, $props);
        return $this;
    }

    /**
     * Add a smallint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnSmallInt(string $name, int $default = 0, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColSmallInt($this->db, $this, $name, $default, $props);
        return $this;
    }

    /**
     * Add a int column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnInt(string $name, int $default = 0, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColInt($this->db, $this, $name, $default, $props);
        return $this;
    }

    /**
     * Add a decimal column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   int             $decimals   Decimals.
     * @param   float           $default    Default value,
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnDecimal(string $name, int $length, int $decimals = 2, float $default = 0.0, 
        array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColDecimal($this->db, $this, $name, $length, $decimals, $default, $props);
        return $this;
    }

    /**
     * Add a text column to this table.
     *
     * @param   string          $name       Column name.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnText(string $name, ?string $default = null, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColText($this->db, $this, $name, $default, $props);
        return $this;
    }

    /**
     * Add a bigint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnBigInt(string $name, int $default = 0, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColBigInt($this->db, $this, $name, $default, $props);
        return $this;
    }

    /**
     * Add a foreign key column to this table.
     *
     * @param   string          $table      Foreign table name.
     * @param   string|null     $onDelete   On delete action.
     * @param   string          $fkCol      Foreign key column.
     * @param   string          $col        Column name.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnFk(string $table, ?string $onDelete = null, string $fkCol = 'id', string $col = 'id',
        array $props = array()) : TableInterface
    {
        $name = 'fk_' . $table . '_' . $fkCol;
        $type = $this->db->platform()->primaryKeyType();

        $references = array('table' => $table, 'column' => $table . '_' . $fkCol);
        
        if (null !== $onDelete) {
            $references['onDelete'] = $onDelete;
        }

        $props = array_merge($props, array('references' => $references));

        $colName = 'fk_' . $table . '_' . $col;
        $this->columns[$colName] = new Col($this->db, $this, $colName, $type, $props);
        return $this;
    }

    /**
     * Add a foreign key column to this table and allow NULL in it.
     *
     * @param   string          $table      Foreign table name.
     * @param   string|null     $onDelete   On delete action.
     * @param   string          $fkCol      Foreign key column.
     * @param   string          $col        Column name.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnFkAllowNull(string $table, ?string $onDelete = null, string $fkCol = 'id', string $col = 'id',
        array $props = array()) : TableInterface
    {
        $name = 'fk_' . $table . '_' . $fkCol;
        $type = $this->db->platform()->primaryKeyType();

        $references = array('table' => $table, 'column' => $table . '_' . $fkCol);
        
        if (null !== $onDelete) {
            $references['onDelete'] = $onDelete;
        }

        $props = array_merge($props, array('references' => $references, 'null' => true));

        $colName = 'fk_' . $table . '_' . $col;
        $this->columns[$colName] = new Col($this->db, $this, $colName, $type, $props);
        return $this;
    }

    /**
     * Add a datetime column to this table.
     *
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  TableInterface
     */
    public function addColumnDateTime(string $name, array $props = array()) : TableInterface
    {
        $this->columns[$name] = new ColDateTime($this->db, $this, $name, $props);
        return $this;
    }

    /**
     * Get the create table SQL
     *
     * @param   bool        $ifNotExists    Only create if table does not exist.
     * @param   bool        $temp           Temporary table?
     * @return  string
     */
    public function getCreateSql(bool $ifNotExists = true, bool $temp = false) : string
    {
        $ret = 'CREATE ';
        if ($temp) {
            $ret .= 'TEMPORARY ';
        }
        $ret .= 'TABLE ';
        if ($ifNotExists) {
            $ret .= 'IF NOT EXISTS ';
        }
        $ret .= $this->db->tn($this->name);
        $ret .= ' (';
        $colSql = array();
        foreach ($this->columns as $column) {
            $colSql[] = $column->getSql();
        }
        $ret .= implode(',', $colSql);

        $refSql = array();
        foreach ($this->columns as $column) {
            $tmp = $column->getReferencesSql();
            if ('' != $tmp) {
                $refSql[] = $tmp;
            }
        }
        if (!empty($refSql)) {
            $ret .= ', ' . implode(',', $refSql);
        }

        $ret .= ')';

        return $ret;
    }

    /**
     * Get the drop table SQL.
     *
     * @param   bool        $ifExists       Only drop if it exists.
     * @return  string
     */
    public function getDropsql(bool $ifExists = true) : string
    {
        $ret = 'DROP TABLE ';
        if ($ifExists) {
            $ret .= 'IF EXISTS ';
        }
        $ret .= $this->db->tn($this->name);
        return $ret;
    }

    /**
     * Create table.
     *
     * @param   bool        $drop           Drop table first?
     * @param   bool        $ifNotExists    Only create if table does not exist.
     * @param   bool        $temp           Temporary table?
     * @return  void
     */
    public function create(bool $drop = true, bool $ifNotExists = true, bool $temp = false)
    {
        if ($drop) {
            $this->drop();
        }
        $this->db->prepare($this->getCreateSql($ifNotExists, $temp))->execute();
        $this->postCreate();
    }

    /**
     * Called after a successful create.
     *
     * @return  void
     */
    protected function postCreate()
    {
    }

    /**
     * Drop table.
     *
     * @param   bool        $ifExists       Only drop if it exists.
     * @return  void
     */
    public function drop(bool $ifExists = true)
    {
        $this->db->prepare($this->getDropsql($ifExists))->execute();
    }
}
