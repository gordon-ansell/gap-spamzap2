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

use GreenFedora\Stdlib\Text\TextBufferInterface;
use GreenFedora\Stdlib\Text\TextDecoratorInterface;
use GreenFedora\Stdlib\Text\TextFormatterInterface;
use GreenFedora\Stdlib\Level;

/**
 * A text buffer of some sort.
 */
class TextBuffer implements TextBufferInterface
{
    /**
     * Message level.
     * @var int
     */
    protected $level = 0;

    /**
     * Decorators.
     * @var TextDecoratorInterface[]
     */
    protected $decorators = [];

    /**
     * Formatters.
     * @var TextFormatterInterface[]
     */
    protected $formatters = [];

    /**
     * End of line character.
     * @var string
     */
    protected $eol = PHP_EOL;

    /**
     * The actual buffer.
     * @var string
     */
    protected $buffer = '';

    protected $count = 0;

    /**
     * Constructor.
     * 
     * @param   int                             $level          Message level.
     * @param   TextDecoratorInterface[]|null   $decorators     Decorators to apply.
     * @param   TextFormatterInterface[]|null   $formatters     Formatters to apply.
     * @return  void
     */
    public function __construct(int $level = 0, $decorators = null, $formatters = null)
    {
        $this->level = $level;
        
        if (!is_null($decorators)) {
            if (!is_array($decorators)) {
                $decorators = [$decorators];
            }
            foreach ($decorators as $decorator) {
                $this->addDecorator($decorator);
            }
        }

        if (!is_null($formatters)) {
            if (!is_array($formatters)) {
                $formatters = [$formatters];
            }
            foreach ($formatters as $formatter) {
                $this->addFormatter($formatter);
            }
        }
    }

    /**
     * Write to the buffer.
     * 
     * @param   string      $text   Text to write.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function write(string $text, array $args = []): TextBufferInterface
    {
        if (count($args) > 0) {
            $this->buffer .= vsprintf($text, $args);
        } else {
            $this->buffer .= $text;
        }
        return $this;
    }

    /**
     * Prepend to the buffer.
     * 
     * @param   string      $text   Text to write.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function prepend(string $text, array $args = []): TextBufferInterface
    {
        if (count($args) > 0) {
            $this->buffer = vsprintf($text, $args) . $this->buffer;
        } else {
            $this->buffer = $text . $this->buffer;
        }
        return $this;
    }

    /**
     * Issue some blank lines.
     * 
     * @param   int         $num        Number of blanks.
     * @param   int         $level      Message level.
     * @param   bool        $finish     Finish the current line first?
     * @return  TextBufferInterface
     */
    public function blank(int $num = 1, int $level = 0, bool $finish = true): TextBufferInterface
    {
        if ($finish and '' !== $this->buffer) {
            $this->end($level);
        }

        for ($i = 0; $i < $num; $i++) {
            $this->writeln('', $level);
        }
        
        return $this;
    }

    /**
     * Write to the buffer and issue a line end.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function writeln(string $text, int $level = 0, array $args = []): TextBufferInterface
    {
        return $this->write($text, $args)
            ->end($level);
    }

    /**
     * Write an array of lines to the output.
     * 
     * @param   array       $lines  Array of lines to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option args.
     * @return  TextBufferInterface
     */
    public function writeArray(array $lines, int $level = 0, array $args = []): TextBufferInterface
    {
        foreach ($lines as $line) {
            $this->writeln($line, $level, $args);
        }
        return $this;
    }

    /**
     * Mark the end of this buffer input. Forces a write to the target.
     * 
     * @param   int     $level          Message level.
     * @param   bool    $evenIfBlank    Write the buffer even if it's blank.
     * @return  TextBufferInterface
     */
    public function end(int $level = 0, bool $evenIfBlank = true): TextBufferInterface
    {
        if ($level < $this->level and 0 !== $level and 0 !== $this->level) {
            $this->buffer = '';
            return $this;
        }

        if ('' === $this->buffer and !$evenIfBlank) {
            return $this;
        }

        $text = $this->format($this->buffer, $level);
        $text = $this->decorate($text, $level);

        $this->writeToTarget($text . $this->eol, $level);

        $this->buffer = '';

        return $this;
    }

    /**
     * Mark the end of this buffer input, unless it's blank. Forces a write to the target.
     * 
     * @param   int     $level  Level.
     * @return  TextBufferInterface
     */
    public function endUnlessBlank(int $level = 0): TextBufferInterface
    {
        return $this->end($level, false);
    }

    /**
     * Write to the actual target.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Error level.
     * @return  TextBufferInterface
     */
    protected function writeToTarget(string $text, int $level): TextBufferInterface
    {
        echo $text;
        return $this;
    }

    /**
     * Decorate the text.
     * 
     * @param   string      $text   Text to decorate.
     * @param   int         $level  Message level.
     * @return  string              Decorated text.
     */
    protected function decorate(string $text, int $level = 0): string
    {
        foreach ($this->decorators as $decorator) {
            $text = $decorator->decorate($text, $level);
        }
        return $text;
    }

    /**
     * Format the text.
     * 
     * @param   string      $text       Text to format.
     * @param   int         $context    Message level.
     * @return  string                  Formatted text.
     */
    protected function format(string $text, int $level = 0): string
    {
        foreach ($this->formatters as $formatter) {
            $text = $formatter->format($text, $level);
        }
        return $text;
    }

    /**
     * Add a decorator.
     * 
     * @param   TextDecoratorInterface  $decorator  Decorator to add.
     * @return  TextBufferInterface
     */
    public function addDecorator(TextDecoratorInterface $decorator): TextBufferInterface
    {
        $this->decorators[] = $decorator;
        return $this;
    }

    /**
     * Prepend a decorator.
     * 
     * @param   TextDecoratorInterface  $decorator  Decorator to prepend.
     * @return  TextBufferInterface
     */
    public function prependDecorator(TextDecoratorInterface $decorator): TextBufferInterface
    {
        array_unshift($this->decorators, $decorator);
        return $this;
    }

    /**
     * Add a formatter.
     * 
     * @param   TextFormatterInterface  $formatter  Formatter to add.
     * @return  TextBufferInterface
     */
    public function addFormatter(TextFormatterInterface $formatter): TextBufferInterface
    {
        $this->formatters[] = $formatter;
        return $this;
    }

    /**
     * Prepend a formatter.
     * 
     * @param   TextFormatterInterface  $formatter  Formatter to prepend.
     * @return  TextBufferInterface
     */
    public function prependFormatter(TextFormatterInterface $formatter): TextBufferInterface
    {
        array_unshift($this->formatters, $formatter);
        return $this;
    }

    /**
     * Quick accessors.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function debug(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::DEBUG, $args);
    }
    public function info(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::INFO, $args);
    }
    public function notice(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::NOTICE, $args);
    }
    public function warning(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::WARNING, $args);
    }
    public function error(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::ERROR, $args);
    }
    public function critical(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::CRITICAL, $args);
    }
    public function alert(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::ALERT, $args);
    }
    public function emergency(string $text, array $args = []): TextBufferInterface
    {
        return $this->writeln($text, Level::EMERGENCY, $args);
    }
}
