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
 * Path helper.
 */
class Path
{
    /**
     * Join some path elements.
     * 
     * @param   string  $start  First part.
     * @param   string  $args   Any number of other parts.
     * @return  string
     */
    public static function join(string $start, string ...$args): string
    {
        $ret = rtrim($start, '\/');

        foreach ($args as $arg) {
            $ret .= DIRECTORY_SEPARATOR . trim($arg, '\/');
        }

        return $ret;
    }

    /**
     * Join some namespace elements.
     * 
     * @param   string  $start  First part.
     * @param   string  $args   Any number of other parts.
     * @return  string
     */
    public static function nsjoin(string $start, string ...$args): string
    {
        $ret = rtrim($start, '\/');

        foreach ($args as $arg) {
            $ret .= '\\' . trim($arg, '\/');
        }

        return $ret;
    }
}
