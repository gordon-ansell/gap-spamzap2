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

use GreenFedora\Db\Schema\SchemaInterface;
use GreenFedora\Db\Schema\ColInterface;


/**
 * Database schema table interface.
 */
interface TableInterface
{
    /**
     * Get the name.
     *
     * @return  string
     */
    public function getName() : string;

    /**
     * Get the parent schema.
     *
     * @return  SchemaInterface
     */
    public function getSchema() : SchemaInterface;

    /**
     * Get a column.
     *
     * @param   string          $name       Column name.
     * @return  ColInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn(string $name) : ColInterface;

    /**
     * Add a column to this table.
     *
     * @param   string|ColInterface     $name       Column name.
     * @param   string|null             $type       Column type.
     * @param   array                   $props      Column properties.
     * @return  self
     */
    public function addColumn($name, ?string $type = null, array $props = array()) : self;

    /**
     * Add a primary auto column to this table.
     *
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnPrimaryAuto(string $name = 'id', array $props = array()) : self;

    /**
     * Add a varchar column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnVarChar(string $name, int $length = 255, ?string $default = '', array $props = array()) : self;

    /**
     * Add a char column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnChar(string $name, int $length = 255, ?string $default = '', array $props = array()) : self;

    /**
     * Add a primary varchar column to this table.
     *
     * @param   int             $length     Length.
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnVarCharPrimary(int $length, string $name = 'id', array $props = array()) : self;

    /**
     * Add a tinyint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnTinyInt(string $name, int $default = 0, array $props = array()) : self;

    /**
     * Add a smallint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnSmallInt(string $name, int $default = 0, array $props = array()) : self;

    /**
     * Add a int column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnInt(string $name, int $default = 0, array $props = array()) : self;

    /**
     * Add a decimal column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $length     Length.
     * @param   int             $decimals   Decimals.
     * @param   float           $default    Default value,
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnDecimal(string $name, int $length, int $decimals = 2, float $default = 0.0, 
        array $props = array()) : self;

    /**
     * Add a text column to this table.
     *
     * @param   string          $name       Column name.
     * @param   string          $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnText(string $name, ?string $default = null, array $props = array()) : self;

    /**
     * Add a bigint column to this table.
     *
     * @param   string          $name       Column name.
     * @param   int             $default    Default value.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnBigInt(string $name, int $default = 0, array $props = array()) : self;

    /**
     * Add a foreign key column to this table.
     *
     * @param   string          $table      Foreign table name.
     * @param   string|null     $onDelete   On delete action.
     * @param   string          $fkCol      Foreign key column.
     * @param   string          $col        Column name.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnFk(string $table, ?string $onDelete = null, string $fkCol = 'id', string $col = 'id',
        array $props = array()) : self;

    /**
     * Add a foreign key column to this table and allow NULL in it.
     *
     * @param   string          $table      Foreign table name.
     * @param   string|null     $onDelete   On delete action.
     * @param   string          $fkCol      Foreign key column.
     * @param   string          $col        Column name.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnFkAllowNull(string $table, ?string $onDelete = null, string $fkCol = 'id', string $col = 'id',
        array $props = array()) : self;

    /**
     * Add a datetime column to this table.
     *
     * @param   string          $name       Column name.
     * @param   array           $props      Column properties.
     * @return  self
     */
    public function addColumnDateTime(string $name, array $props = array()) : self;

    /**
     * Get the create table SQL
     *
     * @param   bool        $ifNotExists    Only create if table does not exist.
     * @param   bool        $temp           Temporary table?
     * @return  string
     */
    public function getCreateSql(bool $ifNotExists = true, bool $temp = false) : string;

    /**
     * Get the drop table SQL.
     *
     * @param   bool        $ifExists       Only drop if it exists.
     * @return  string
     */
    public function getDropsql(bool $ifExists = true) : string;

    /**
     * Create table.
     *
     * @param   bool        $drop           Drop table first?
     * @param   bool        $ifNotExists    Only create if table does not exist.
     * @param   bool        $temp           Temporary table?
     * @return  void
     */
    public function create(bool $drop = true, bool $ifNotExists = true, bool $temp = false);

    /**
     * Drop table.
     *
     * @param   bool        $ifExists       Only drop if it exists.
     * @return  void
     */
    public function drop(bool $ifExists = true);
}
