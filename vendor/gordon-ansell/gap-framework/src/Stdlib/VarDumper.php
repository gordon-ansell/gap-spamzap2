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
namespace GreenFedora\Stdlib;

/**
 * Variable dumper.
 */
class VarDumper
{
    /**
     * Dump a variable.
     * 
     * @param   mixed   $var    Variable to dump.
     * @param   int     $depth  Depth to dum arrays to.
     * @param   int     $level  Current level.
     * @return  string
     */
    public static function dump($var, int $depth = 10, int $level = 1)
    {
        switch (gettype($var)) {
            case 'string':
                return $var;
                break;
            case 'boolean':
                return (true == $var) ? "true" : "false";
                break;
            case 'integer':
            case 'double':
                return strval($var);
                break;
            case 'NULL':
                return 'NULL';
                break;
            case 'resource':
                return 'resource';
                break;
            case 'array':
                return self::dumpArray($var, $depth, $level);
                break;
            case 'object':
                if (method_exists($var, '__toString')) {
                    return $var->__toString();
                } else if (method_exists($var, 'toArray')) {
                    return self::dumpArray($var->toArray(), $depth, $level);
                } else {
                    return 'object (' . get_class($var) . ')';
                }
                break;
            default:
                return 'Unknown type';
        }
    }

    /**
     * Dump an array.
     * 
     * @param   array   $var    Array to dump.
     * @param   int     $depth  Depth to dump to.
     * @param   int     $level  Current level.
     * @return  string
     */
    public static function dumpArray(array $var, int $depth = 10, int $level = 1): string 
    {
        if ($depth <= $level) {
            return '[...]';
        } else if (empty($var)) {
            return '[]';
        } else {
            $keys = array_keys($var);
            $padding = str_repeat(' ', $level * 3);
            $ret = '[';
            foreach ($keys as $key) {
                $ret .= PHP_EOL . $padding . '   ';
                $ret .= self::dump($key, $depth, 0);
                $ret .= ' => ';
                $ret .= self::dump($var[$key], $depth, $level + 1);
            }
            $ret .= PHP_EOL . $padding . ']';
            return $ret;
        }
    }
}
