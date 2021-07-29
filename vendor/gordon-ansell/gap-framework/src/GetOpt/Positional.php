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
namespace GreenFedora\GetOpt;

use GreenFedora\GetOpt\PositionalInterface;
use GreenFedora\GetOpt\ConditionalPositional;
use GreenFedora\GetOpt\Parameter;

/**
 * Command line positional parameter.
 */
class Positional extends Parameter implements PositionalInterface
{

    /**
     * Constructor.
     * 
     * @param   string      $name           Argument name.
     * @param   string      $description    Description.
     * @param   int|null    $flags          Flags.
     * @param   array|null  $choices        Choices.
     * @param   mixed       $default        Default value,
     * @return  void
     */
    public function __construct(string $name, string $description, ?int $flags = self::COMPULSORY, ?array $choices = null,
    $default = null)
    {
        if (is_null($flags)) {
            $flags = self::COMPULSORY;
        }
        parent::__construct($name, $description, $flags, $default, $choices);
    }

    /**
     * Add a conditional.
     * 
     * @param   string      $whereTest      Must be 'command' or 'action'.
     * @param   mixed       $whereValue     What value to test.
     * @param   int         $flags          What we're actually doing.
     * @return  PositionalInterface
     */
    public function addConditional(string $whereTest, $whereValue, int $flags = 1): PositionalInterface
    {
        if (!is_array($whereValue)) {
            $whereValue = [$whereValue];
        }
        foreach ($whereValue as $val) {
            $this->conditionals[] = new ConditionalPositional($this, $whereTest, $val, $flags);
        }
        return $this;
    }

    /**
     * Is this an array?
     * 
     * @return  bool
     */
    public function isArray(): bool
    {
        return (self::ARRAYVAL === ($this->flags & self::ARRAYVAL));
    }

    /**
     * Is this the command?
     * 
     * @return  bool
     */
    public function isCommand(): bool
    {
        return (self::COMMAND === ($this->flags & self::COMMAND));
    }

    /**
     * Set this as the command.
     * 
     * @return  PositionalInterface
     */
    public function setAsCommand(): PositionalInterface
    {
        $this->flags |= self::COMMAND;
        return $this;
    }

    /**
     * Set this as the action.
     * 
     * @return  PositionalInterface
     */
    public function setAsAction(): PositionalInterface
    {
        $this->flags |= self::ACTION;
        return$this;
    }

    /**
     * Is this the action?
     * 
     * @return  bool
     */
    public function isAction(): bool
    {
        return (self::ACTION === ($this->flags & self::ACTION));
    }

}
