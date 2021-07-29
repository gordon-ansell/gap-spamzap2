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

use GreenFedora\Console\Output\StreamOutput;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Stdlib\Text\TextBufferInterface;
use GreenFedora\Console\Output\Exception\InvalidArgumentException;
use GreenFedora\Stdlib\Level;

/**
 * Console stream output.
 */
class ConsoleOutput extends StreamOutput implements OutputInterface
{
    /**
     * Error stream.
     * @var resource
     */
    protected $errorStream = null;

    /**
     * Constructor.
     * 
     * @param   int                             $level          Message level.
     * @param   bool                            $wantColours    Do we want colours?
     * @param   TextDecoratorInterface[]|null   $decorators     Decorators to apply.
     * @param   TextFormatterInterface[]|null   $formatters     Formatters to apply.
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct(int $level = 0, bool $wantColours = true, $decorators = null, $formatters = null)
    {
        if (is_null($decorators) and $wantColours) {
            $decorators = new ConsoleColourDecorator();
        }
        parent::__construct(@fopen('php://stdout', 'w') ?: fopen('php://output', 'w'), $level, $decorators, $formatters);
    }

    /**
     * Open the error stream.
     * 
     * @return resource
     */
    protected function openErrorStream()
    {
        return @fopen('php://stderr', 'w') ?: fopen('php://output', 'w');        
    }

    /**
     * Write to the actual target.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * 
     * @return  TextBufferInterface
     */
    protected function writeToTarget(string $text, int $level): TextBufferInterface
    {
        if ($level >= Level::ERROR) {
            if (is_null($this->errorStream)) {
                $this->errorStream = $this->openErrorStream();
            }
            @fwrite($this->errorStream, $text);
            fflush($this->errorStream);
        } else {
            @fwrite($this->stream, $text);
            fflush($this->stream);
        }
        return $this;
    }
}
