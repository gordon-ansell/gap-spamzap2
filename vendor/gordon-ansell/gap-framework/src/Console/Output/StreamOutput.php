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
namespace GreenFedora\Console\Output;

use GreenFedora\Console\Output\Output;
use GreenFedora\Stdlib\Text\TextBufferInterface;
use GreenFedora\Console\Output\Exception\InvalidArgumentException;

/**
 * Stream output.
 */
class StreamOutput extends Output
{
    /**
     * The stream itself.
     */
    protected $stream = null;

    /**
     * Constructor.
     * 
     * @param   resource                        $stream         The stream.
     * @param   int                             $level          Message level.
     * @param   TextDecoratorInterface[]|null   $decorators     Decorators to apply.
     * @param   TextFormatterInterface[]|null   $formatters     Formatters to apply.
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct($stream, int $level = 0, $decorators = null, $formatters = null)
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new InvalidArgumentException('The StreamOutput class needs a stream as its first argument.');
        }
        $this->stream = $stream;
        parent::__construct($level, $decorators, $formatters);
    }

    /**
     * Write to the actual target.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @return  TextBufferInterface
     */
    protected function writeToTarget(string $text, int $level): TextBufferInterface
    {
        @fwrite($this->stream, $text);
        fflush($this->stream);
        return $this;
    }
}
