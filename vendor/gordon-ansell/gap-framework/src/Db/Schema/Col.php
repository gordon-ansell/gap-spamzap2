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
namespace GreenFedora\Db\Schema;

use GreenFedora\Db\Platform\PlatformInterface;

use GreenFedora\Db\Schema\TableInterface;

use GreenFedora\Db\Schema\Exception\DbSchemaException;

use GreenFedora\Db\Schema\ColInterface;
use GreenFedora\Db\DbInterface;

/**
 * Database schema column.
 */
class Col implements ColInterface
{
   /**
     * Parent table.
     * @var TableInterface
     */
    protected $table = null;

    /**
     * Database.
     * @var DbInterface
     */
    protected $db = null;

    /**
     * Column name.
     * @var string
     */
    protected $name = '';

    /**
     * Column type.
     * @var string
     */
    protected $type = '';

    /**
     * Column properties.
     * @var array
     */
    protected $props = array();

    /**
     * Column spec.
     * @var array
     */
    protected $spec = array();

    /**
     * Constructor.
     *
     * @param   DbInterface         $db         Database entry point.
     * @param   TableInterface      $table      Parent table.
     * @param   string              $name       Column name.
     * @param   string              $type       Column type.
     * @param   array               $props      Column properties.
     * @return  void
     */
    public function __construct(DbInterface $db, TableInterface $table, string $name, string $type, array $props = array())
    {
        $this->db = $db;
        $this->table = $table;
        $this->name = $name;
        $this->type = strtoupper($type);
        $this->props = $props;
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
     * Get the parent table.
     *
     * @return  TableInterface
     */
    public function getTable() : TableInterface
    {
        return $this->table;
    }

    /**
     * Get a property or default.
     *
     * @param   string      $name       Property name.
     * @param   mixed       $default    Default.
     * @return  mixed
     */
    public function getProp(string $name, $default = null)
    {
        if (array_key_exists($name, $this->props)) {
            return $this->props[$name];
        }
        return $default;
    }

    /**
     * Get all properties.
     *
     * @return  array
     */
    public function getProps() : array
    {
        return $this->props;
    }

    /**
     * See if this column type has a particular flag.
     *
     * @param   int         $flag       Flag to test.
     * @return  bool
     */
    protected function hasFlag(int $flag) : bool
    {
        if (array_key_exists('flags', $this->spec)) {
            return (($this->spec['flags'] & $flag) == $flag);
        }
        return false;
    }

    /**
     * Get the type sql.
     *
     * @return  string
     * @throws  DbSchemaException
     */
    protected function getTypeSql() : string
    {
        $ret = strtoupper($this->type);

        // Length, decimals.
        if ($this->hasFlag(PlatformInterface::CP_LEN)) {
            $len = $this->getProp('length', false);
            if ((false === $len) and ($this->hasFlag(PlatformInterface::CP_LEN_COMP))) {
                throw new DbSchemaException(sprintf("Length is compulsory for '%s' columns, for column name '%s'", $this->type, $this->name));
                return '';
            }

            if (false !== $len) {
                $ret .= '(' . $len;

                if ($this->hasFlag(PlatformInterface::CP_DEC)) {
                    $dec = $this->getProp('decimals', false);

                    if ((false === $dec) and ($this->hasFlag(PlatformInterface::CP_DEC_COMP_IF_LEN))) {
                        throw new DbSchemaException(sprintf("Decimals is compulsory for '%s' columns that have length, for column name '%s'", $this->type, $this->name));
                        return '';
                    }

                    if (false !== $dec) {
                        $ret .= ',' . $dec;
                    }
                }
                
                $ret .= ')';
            }

        // Fsp.
        } else if ($this->hasFlag(PlatformInterface::CP_FSP)) {
            $fsp = $this->getProp('fsp', false);
            if (false !== $fsp) {
                $ret .= '(' . $fsp . ')';
            }

        // Values.
        } else if ($this->hasFlag(PlatformInterface::CP_VALS)) {
            $values = $this->getProp('values', array());
            if ((empty($values)) or (!is_array($values))) {
                throw new DbSchemaException(sprintf("Values are compulsory for '%s' columns, for column name '%s'", $this->type, $this->name));
                return '';
            }
            $ret .= '(' . implode(',', $values) . ')';
        }

        // Unsigned, zerofill.
        if ($this->hasFlag(PlatformInterface::CP_UNSIGNED)) {
            $unsigned = $this->getProp('unsigned', true);
            $zerofill = $this->getProp('zerofill', false);

            if ($unsigned) {
                $ret .= ' ' . $this->db->platform()->unsigned();
            }
            if ($zerofill) {
                $ret .= ' ZEROFILL';
            }
        }

        // Binary.
        if ($this->hasFlag(PlatformInterface::CP_BINARY)) {
            $binary = $this->getProp('binary', false);
            if ($binary) {
                $ret .= ' BINARY';
            }
        }

        // Charset, collate.
        if ($this->hasFlag(PlatformInterface::CP_CHARSET)) {
            $charset = $this->getProp('charset', false);
            $collate = $this->getProp('collate', false);

            if ($charset) {
                $ret .= ' CHARACTER SET ' . $charset;
            }
            if ($collate) {
                $ret .= ' COLLATE ' . $collate;
            }
        }

        return $ret;
    }

    /**
     * Get the references SQL.
     *
     * @return  string
     * @throws  DbSchemaException
     */
    public function getReferencesSql() : string
    {
        $ret = '';
        $refs = $this->getProp('references', false);
        if ((false !== $refs) and (is_array($refs))) {
            $table = array_key_exists('table', $refs) ? $refs['table'] : null;
            if (null === $table) {
                throw new DbSchemaException(sprintf("Reference must specify a 'table', for column '%s'", $this->name));
                return '';
            }

            $col = array_key_exists('column', $refs) ? $refs['column'] : null;
            if (null === $col) {
                throw new DbSchemaException(sprintf("Reference must specify a 'column', for column '%s'", $this->name));
                return '';
            }

            $ret = ' FOREIGN KEY (' . $this->db->idq($this->name);
            $ret .= ') REFERENCES ' . $this->db->tn($table);
            $col = !is_array($col) ? array($col) : $col;
            $final = array();
            foreach ($col as $item) {
                $final[] = $this->db->idq($item);
            }
            $ret .= ' (' . implode(',', $final) . ')';

            if (array_key_exists('match', $refs)) {
                $ret .= ' MATCH ' . strtoupper($refs['match']);
            }

            if (array_key_exists('onDelete', $refs)) {
                $ret .= ' ON DELETE ' . strtoupper($refs['onDelete']);
            }
            if (array_key_exists('onUpdate', $refs)) {
                $ret .= ' ON UPDATE ' . strtoupper($refs['onUpdate']);
            }
        }

        return $ret;
    }

    /**
     * Get the column SQL
     *
     * @return  string
     * @throws  DbSchemaException
     */
    public function getSql() : string
    {
        $ret = $this->db->idq($this->name);

        // Load the column spec.
        $spec = $this->db->platform()->getColType($this->type);
        if (null === $spec) {
            throw new DbSchemaException(sprintf("'%s' is an invalid column type, for column '%s'", $this->type, $this->name));
            return '';
        } else {
            $this->spec = $spec;
        }

        // Add the type.
        $ret .= ' ' . $this->getTypeSql();

        // Null?
        if (true === $this->getProp('primary', false)) {
            if (true === $this->db->platform()->pkAllowNull()) {
                $ret .= ' NULL';
            } else if (false === $this->db->platform()->pkAllowNull()) {
                $ret .= ' NOT NULL';
            }
        } else {
            if (true === $this->getProp('null', false)) {
                $ret .= ' NULL';
            } else {
                $ret .= ' NOT NULL';
            }
        }

        // Default.
        $def = $this->getProp('default', 'NO-DEFAULT');
        if ($def !== 'NO-DEFAULT') {
            if (is_null($def)) {
                $ret .= ' DEFAULT NULL';
            } else if (!is_string($def)) {
                $ret .= ' DEFAULT ' . $def;
            } else {
                $ret .= " DEFAULT '" . $def . "'";
            }
        }

        // Auto.
        if (true === $this->getProp('auto', false)) {
            $ret .= ' ' . $this->db->platform()->autoIncrement();
        }

        // Primary, unique.
        if (true === $this->getProp('primary', false)) {
            $ret .= ' ' . $this->db->platform()->primary();
        } else if (true === $this->getProp('unique', false)) {
            $ret .= ' ' . $this->db->platform()->unique();
        }

        // Comment.
        $comment = $this->getProp('comment', false);
        if (false !== $comment) {
            $ret .= " COMMENT '" . $comment . "'";
        }

        // Column format.
        $format = $this->getProp('format', false);
        if (false !== $format) {
            $ret .= ' COLUMN_FORMAT ' . strtoupper($format);
        }

        // Storage.
        $storage = $this->getProp('storage', false);
        if (false !== $storage) {
            $ret .= ' STORAGE ' . strtoupper($storage);
        }

        // References.
        //$ret .= $this->getReferencesSql();

        // Return the definition sql.
        return $ret;
    }
}
