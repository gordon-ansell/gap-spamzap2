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
use GreenFedora\Stdlib\Text\ArrayTextBufferInterface;

/**
 * A text buffer that just writes out to an array.
 */
class ArrayTextBuffer extends TextBuffer implements ArrayTextBufferInterface
{
    /**
     * The data array.
     * @var array
     */
    protected $data = [];

    /**
     * End of line character.
     * @var string
     */
    protected $eol = '';

    /**
     * Write to the actual target.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @return  ArrayTextBufferInterface
     */
    protected function writeToTarget(string $text, int $level): TextBufferInterface
    {
        $this->data[] = $text;
        return $this;
    }

    /**
     * Get the data.
     * 
     * @return  array
     */
    public function getData(): array
    {
        return $this->data;
    }

}
