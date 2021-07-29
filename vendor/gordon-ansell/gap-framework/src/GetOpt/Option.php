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

use GreenFedora\GetOpt\Exception\InvalidArgumentException;
use GreenFedora\GetOpt\OptionInterface;
use GreenFedora\GetOpt\ConditionalOption;
use GreenFedora\GetOpt\Parameter;


/**
 * Command line option.
 */
class Option extends Parameter implements OptionInterface
{
    /**
     * Shortcut
     * @var string|null
     */
    protected $shortcut = null;

    /**
     * Argument name.
     * @var string|null
     */
    protected $argumentName = null;

    /**
     * Is this a real value (as opposed to switch that's just on).
     * @var bool
     */
    protected $realValue = false;

    /**
     * Constructor.
     * 
     * @param   string          $name               Argument name.
     * @param   string          $description        Description.
     * @param   int|null        $flags              Flags.
     * @param   string|null     $shortcut           Shortcut.
     * @param   string|null     $argumentName       Argument name.
     * @param   string|null     $default            Default.  
     * @param   array|null      $choices            Choices.
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct(string $name, string $description, ?int $flags = self::OPTIONAL, ?string $shortcut = null,
    ?string $argumentName = null, ?string $default = null, ?array $choices = null)
    {
        if (is_null($flags)) {
            $flags = self::OPTIONAL;
        }

        if (self::COMPULSORY === (self::COMPULSORY & $flags)) {
            $default = null;
        }

        parent::__construct($name, $description, $flags, $default, $choices);

        if (!is_null($shortcut) and strlen($shortcut) > 1) {
            throw new InvalidArgumentException("Option shortcuts can only be one character long.");
        }

        $this->shortcut = $shortcut;
        $this->argumentName = $argumentName;
    }

    /**
     * Add a conditional.
     * 
     * @param   string      $whereTest      Must be 'command' or 'action'.
     * @param   mixed       $whereValue     What value to test.
     * @param   int         $flags          What we're actually doing.
     * @return  OptionInterface
     */
    public function addConditional(string $whereTest, $whereValue, int $flags = 1): OptionInterface
    {
        if (!is_array($whereValue)) {
            $whereValue = [$whereValue];
        }
        foreach ($whereValue as $val) {
            $this->conditionals[] = new ConditionalOption($this, $whereTest, $val, $flags);
        }
        return $this;
    }

    /**
     * Get the shortcut.
     * 
     * @return  string|null
     */
    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    /**
     * Do we have a shortcut? If so, what is it?
     * 
     * @param   string|null     $test   If we want to test a particular shortcut, pass non-null.
     * @return  bool
     */
    public function hasShortcut(?string $test = null): bool
    {
        if (is_null($test)) {
            return !is_null($this->shortcut);
        }
        return (!is_null($this->shortcut) and $test === $this->shortcut);
    }

    /**
     * Do we have an argument name.
     * 
     * @return  bool
     */
    public function hasArgumentName(): bool
    {
        return (!is_null($this->argumentName));
    }

    /**
     * Get the argument name.
     * 
     * @return  string|null
     */
    public function getArgumentName(): ?string
    {
        return $this->argumentName;
    }

    /**
     * Has an argument?
     * 
     * @return  bool
     */
    public function hasArgument(): bool
    {
        return (!is_null($this->argumentName));
    }

    /**
     * Is this a real value?
     * 
     * @return bool
     */
    public function isRealValue(): bool
    {
        return $this->realValue;
    }

    /**
     * Set the real value flag.
     * 
     * @param   bool    $flag   Flag value.
     * @return  OptionInterface
     */
    public function setIsRealValue(bool $flag = true): OptionInterface
    {
        $this->realValue = $flag;
        return $this;
    }

    /**
     * Get the value.
     * 
     * @return  mixed
     */
    public function getValue()
    {
        if (!is_null($this->value)) {
            return ($this->isRealvalue()) ? strval($this->value) : $this->value;
        } else if ($this->hasArgument() and $this->hasDefault()) {
            return $this->default;
        }
        return null;
    }

}
