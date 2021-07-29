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
namespace GreenFedora\Db\Platform;

use GreenFedora\Db\Platform\PlatformInterface;
use GreenFedora\Db\Platform\AbstractPlatform;

/**
 * Database SQLite platform.
 */
class SQLite extends AbstractPlatform implements PlatformInterface
{
    /**
     * ID quote character.
     * @var string
     */
    protected $idqChar = '`';

    /**
     * Column types.
     * @var array
     */
    protected $colTypes = array(

        'BIT'           =>  array('flags' => self::CP_LEN, 'max' => 64),

        'TINYINT'       =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED, 'alias' => 'INT(3)'),
        'SMALLINT'      =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED),
        'MEDIUMINT'     =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED, 'alias' => 'INT(7)'),
        'INT'           =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED),
        'INTEGER'       =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED),
        'BIGINT'        =>  array('flags' => self::CP_LEN | self::CP_UNSIGNED, 'alias' => 'INT(20)'),

        'REAL'          =>  array('flags' => self::CP_LEN | self::CP_DEC | self::CP_DEC_COMP_IF_LEN | self::CP_UNSIGNED),
        'DOUBLE'        =>  array('flags' => self::CP_LEN | self::CP_DEC | self::CP_DEC_COMP_IF_LEN | self::CP_UNSIGNED),
        'FLOAT'         =>  array('flags' => self::CP_LEN | self::CP_DEC | self::CP_DEC_COMP_IF_LEN | self::CP_UNSIGNED),

        'DECIMAL'       =>  array('flags' => self::CP_LEN | self::CP_DEC | self::CP_UNSIGNED),
        'NUMERIC'       =>  array('flags' => self::CP_LEN | self::CP_DEC | self::CP_UNSIGNED),

        'TIME'          =>  array('flags' => self::CP_FSP),
        'TIMESTAMP'     =>  array('flags' => self::CP_FSP),
        'DATETIME'      =>  array('flags' => self::CP_FSP),

        'CHAR'          =>  array('flags' => self::CP_LEN | self::CP_CHARSET | self::CP_BINARY),
        'VARCHAR'       =>  array('flags' => self::CP_LEN | self::CP_LEN_COMP | self::CP_CHARSET | self:: CP_BINARY),

        'BINARY'        =>  array('flags' => self::CP_LEN),
        'VARBINARY'     =>  array('flags' => self::CP_LEN | self::CP_LEN_COMP),

        'TINYTEXT'      =>  array('flags' => self::CP_CHARSET | self::CP_BINARY),
        'TEXT'          =>  array('flags' => self::CP_CHARSET | self::CP_BINARY),
        'MEDIUMTEXT'    =>  array('flags' => self::CP_CHARSET | self::CP_BINARY),
        'LONGTEXT'      =>  array('flags' => self::CP_CHARSET | self::CP_BINARY),

        'ENUM'          =>  array('flags' => self::CP_VALS | self::CP_CHARSET),
        'SET'           =>  array('flags' => self::CP_VALS | self::CP_CHARSET),

        'DATE'          =>  array(),
        'YEAR'          =>  array(),
        'TINYBLOB'      =>  array(),
        'BLOB'          =>  array(),
        'MEDIUMBLOB'    =>  array(),
        'LONGBLOB'      =>  array(),
        'JSON'          =>  array(),

        'GEOMETRY'      =>  array(),
        'POINT'         =>  array(),
        'LINESTRING'    =>  array(),
        'POLYGON'       =>  array(),

        'MULTIPOINT'         =>  array(),
        'MULTILINESTRING'    =>  array(),
        'MULTIPOLYGON'       =>  array(),
        'GEOMETRYCOLLECTION' =>  array(),
    );

    /**
     * Column modifiers.
     * @var array
     */
    protected $colMods = [
        'autoincrement'             => '',
        'primary'                   => 'PRIMARY KEY',
        'unique'                    => 'UNIQUE',
        'unsigned'                  => '',
        'primarykeytype'            => 'INTEGER',
        'pkallownull'               => null,
    ];
}
