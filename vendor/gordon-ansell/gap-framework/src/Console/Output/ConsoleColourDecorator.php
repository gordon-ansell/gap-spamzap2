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

use GreenFedora\Stdlib\Text\TextDecoratorInterface;
use GreenFedora\Stdlib\Level;
use GreenFedora\Console\Output\Ansi;
use GreenFedora\Console\Output\Exception\OutOfBoundsException;

/**
 * Apply colours to messages.
 */
class ConsoleColourDecorator implements TextDecoratorInterface
{
    /**
     * Colour schemes.
     * @var array
     */
    protected $colourSchemes = [
        'standard'  => [
            Level::TAG_DEBUG     => [Ansi::STYLE_INTENSITY_FAINT],
            Level::TAG_INFO      => [Ansi::COLOR_FG_RESET],
            Level::TAG_NOTICE    => [Ansi::COLOR_FG_GREEN],
            Level::TAG_WARNING   => [Ansi::COLOR_FG_PURPLE],
            Level::TAG_ERROR     => [Ansi::COLOR_FG_RED],
            Level::TAG_CRITICAL  => [Ansi::STYLE_BOLD, Ansi::COLOR_FG_RED],
            Level::TAG_ALERT     => [Ansi::STYLE_BOLD, Ansi::COLOR_FG_RED, Ansi::COLOR_BG_YELLOW],
            Level::TAG_EMERGENCY => [Ansi::STYLE_BOLD, Ansi::STYLE_BLINK, Ansi::COLOR_FG_WHITE, Ansi::COLOR_BG_RED],
        ]
    ];

    /**
     * Active scheme.
     * @var string
     */
    protected $scheme = 'standard';

    /**
     * Constructor.
     * 
     * @param   string      $scheme         Active scheme.
     * @param   array[]     $schemeDefs     Scheme definitions.
     * @return  void
     */
    public function __construct(string $scheme = 'standard', array $schemeDefs = [])
    {
        if (count($schemeDefs) > 0) {
            foreach ($schemeDefs as $name => $schemeDef) {
                $this->colourSchemes[$name] = $schemeDef;
            }
        }
        $this->setScheme($scheme);
    }

    /**
     * Set the scheme.
     * 
     * @param   string  $scheme         Scheme to set.
     * @return  string                  Old scheme.
     * @throws  OutOfBoundsException
     */
    public function setScheme(string $scheme): string
    {
        if (!array_key_exists($scheme, $this->colourSchemes)) {
            throw new OutOfBoundsException(sprintf("Scheme '%s' is not defined and cannot therefore be activated.", $scheme));
        }

        $saved = $this->scheme;
        $this->scheme = $scheme;
        return $saved;
    }

    /**
     * Get the current scheme.
     * 
     * @return  string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Add a colour scheme.
     * 
     * @param   string  $name           Name.
     * @param   array   $schemeDef      Scheme def.
     * @return  TextDecoratorInterface
     */
    public function addScheme(string $name, array $schemeDef): TextDecoratorInterface
    {
        $this->colourSchemes[$name] = $schemeDef;
        return $this;
    }

    /**
     * Decorate the text.
     * 
     * @param   string  $text   Text to be decorated.
     * @param   int     $level  Message level.
     * @return  string          Decorated text.
     */
    public function decorate(string $text, int $level = 0): string
    {
        $ln = Level::l2t($level);
        if (array_key_exists($this->scheme, $this->colourSchemes) 
        and array_key_exists($ln, $this->colourSchemes[$this->scheme])) {
            $cc = '';
            if (count($this->colourSchemes[$this->scheme]) > 0) {
                $cc = implode(';', $this->colourSchemes[$this->scheme][$ln]);
            }
            if ('' !== $cc) {
                $cc = Ansi::cs($cc);
            }

            $text = $cc . $text . Ansi::cs(Ansi::RESET);
        }

        return $text;
    }


}
