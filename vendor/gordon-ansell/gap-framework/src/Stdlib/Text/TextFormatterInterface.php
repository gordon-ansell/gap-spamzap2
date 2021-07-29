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
namespace GreenFedora\Stdlib\Text;


/**
 * Formats text in some way.
 */
interface TextFormatterInterface
{
    /**
     * Format the text.
     * 
     * @param   string  $text       Text to be formatted.
     * @param   int     $level      Level.
     * @return  string              Decorated text.
     */
    public function format(string $text, int $level = 0): string;
}
