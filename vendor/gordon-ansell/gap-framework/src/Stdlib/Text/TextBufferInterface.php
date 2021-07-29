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
 * Interface for the TextBuffer class.
 */
interface TextBufferInterface
{
    /**
     * Write to the buffer.
     * 
     * @param   string      $text   Text to write.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function write(string $text, array $args = []): TextBufferInterface;

    /**
     * Prepend to the buffer.
     * 
     * @param   string      $text   Text to write.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function prepend(string $text, array $args = []): TextBufferInterface;

    /**
     * Issue some blank lines.
     * 
     * @param   int         $num        Number of blanks.
     * @param   int         $level      Message level.
     * @param   bool        $finish     Finish the current line first?
     * @return  TextBufferInterface
     */
    public function blank(int $num = 1, int $level = 0, bool $finish = true): TextBufferInterface;

    /**
     * Write to the buffer and issue a line end.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function writeln(string $text, int $level = 0, array $args = []): TextBufferInterface;

    /**
     * Write an array of lines to the output.
     * 
     * @param   array       $lines  Array of lines to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option args.
     * @return  TextBufferInterface
     */
    public function writeArray(array $lines, int $level = 0, array $args = []): TextBufferInterface;

    /**
     * Mark the end of this buffer input. Forces a write to the target.
     * 
     * @param   int     $level          Message level.
     * @param   bool    $evenIfBlank    Write the buffer even if it's blank.
     * @return  TextBufferInterface
     */
    public function end(int $level = 0, bool $evenIfBlank = true): TextBufferInterface;

    /**
     * Mark the end of this buffer input, unless it's blank. Forces a write to the target.
     * 
     * @param   int     $level  Message level.
     * @return  TextBufferInterface
     */
    public function endUnlessBlank(int $level = 0): TextBufferInterface;

    /**
     * Add a decorator.
     * 
     * @param   TextDecoratorInterface  $decorator  Decorator to add.
     * @return  TextBufferInterface
     */
    public function addDecorator(TextDecoratorInterface $decorator): TextBufferInterface;

    /**
     * Prepend a decorator.
     * 
     * @param   TextDecoratorInterface  $decorator  Decorator to prepend.
     * @return  TextBufferInterface
     */
    public function prependDecorator(TextDecoratorInterface $decorator): TextBufferInterface;

    /**
     * Add a formatter.
     * 
     * @param   TextFormatterInterface  $formatter  Formatter to add.
     * @return  TextBufferInterface
     */
    public function addFormatter(TextFormatterInterface $formatter): TextBufferInterface;

    /**
     * Prepend a formatter.
     * 
     * @param   TextFormatterInterface  $formatter  Formatter to prepend.
     * @return  TextBufferInterface
     */
    public function prependFormatter(TextFormatterInterface $formatter): TextBufferInterface;

    /**
     * Quick accessors.
     * 
     * @param   string      $text   Text to write.
     * @param   int         $level  Level.
     * @param   array       $args   Option arguments.
     * @return  TextBufferInterface
     */
    public function debug(string $text, array $args = []): TextBufferInterface;
    public function info(string $text, array $args = []): TextBufferInterface;
    public function notice(string $text, array $args = []): TextBufferInterface;
    public function warning(string $text, array $args = []): TextBufferInterface;
    public function error(string $text, array $args = []): TextBufferInterface;
    public function critical(string $text, array $args = []): TextBufferInterface;
    public function alert(string $text, array $args = []): TextBufferInterface;
    public function emergency(string $text, array $args = []): TextBufferInterface;
}
