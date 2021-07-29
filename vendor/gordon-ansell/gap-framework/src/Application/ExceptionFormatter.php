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
namespace GreenFedora\Application;

use GreenFedora\Stdlib\VarDumper;

/**
 * Exception formatter.
 */
class ExceptionFormatter
{
    /**
     * Format the exception.
     * 
     * @param   \Throwable  $e     Exception to format.
     * @return  array              Array of lines in the exception output.
     */
    public static function format(\Throwable $e): array
    {
        $ret = [];

        $ret[] = "Exception: " . $e->getMessage();
        $ret[] = "Thrown on line: " . $e->getLine();
        $ret[] = "of file: " . $e->getFile();
        $ret[] = "Stack trace: ";

        foreach ($e->getTrace() as $k => $v) {
            $ret [] = "---" . $k . ": ";
            foreach ($v as $k1 => $v1) {
                /*
                if (is_array($v1)) {
                    $arr = '';
                    foreach ($v1 as $parm) {
                        if ('' != $arr) {
                            $arr .= ', ';
                        }
                        if (is_object($parm)) {
                            $arr .= get_class($parm);
                        } elseif (is_array($parm)) {
                            $arr .= 'array(' . implode(', ', $parm) . ')';
                        } else {
                            $arr .= $parm;
                        }
                    }
                    $v1 = $arr;
                }
                */
                $ret[] = "-------" . $k1 . ": " . VarDumper::dump($v1);
            }
        }

        return $ret;
    }

}