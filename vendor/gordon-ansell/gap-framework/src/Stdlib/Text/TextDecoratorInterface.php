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

use GreenFedora\Stdlib\Text\TextBuffer;

/**
 * Decorates text in some way.
 */
interface TextDecoratorInterface
{
    /**
     * Decorate the text.
     * 
     * @param   string  $text   Text to be decorated.
     * @param   int     $level  Message level.
     * @return  string          Decorated text.
     */
    public function decorate(string $text, int $level = 0): string;
}
