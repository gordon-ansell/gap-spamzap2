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

use GreenFedora\GetOpt\ParameterInterface;
use GreenFedora\Validator\ValidatorCollection;
use GreenFedora\GetOpt\ConditionalInterface;

/**
 * Command line parameter.
 */
class Parameter implements ParameterInterface
{
    /**
     * Flags.
     */
    const OPTIONAL      =   1;
    const COMPULSORY    =   2;
    const ARRAYVAL      =   4;
    const COMMAND       =   8;
    const ACTION        =   16;
    const HELPIGNORE    =   32;
    const RELAXCHOICES  =   64;

    /**
     * Name.
     * @var string|null
     */
    protected $name = null;

    /**
     * Description.
     * @var string|null
     */
    protected $description = null;

    /**
     * Flags.
     * @var int
     */
    protected $flags = 0;

    /**
     * Choices.
     * @var array|null
     */
    protected $choices = null;

    /**
     * Value.
     * @var mixed
     */
    protected $value = null;

    /**
     * Default.
     * @var mixed
     */
    protected $default = null;

    /**
     * Validators.
     * @var ValidatorCollection|null
     */
    protected $validators = null;

    /**
     * Conditionals.
     * @var ConditionalInterface[]
     */
    protected $conditionals = [];

    /**
     * Constructor.
     * 
     * @param   string          $name               Argument name.
     * @param   string          $description        Description.
     * @param   int|null        $flags              Flags.
     * @param   string|null     $default            Default.  
     * @param   array|null      $choices            Choices.
     * @return  void
     */
    public function __construct(string $name, string $description, ?int $flags = 0, ?string $default = null, 
    ?array $choices = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->flags = $flags;
        $this->default = $default;
        $this->choices = $choices;
    }

    /*
     * Add a validator.
     * 
     * @param   string          $validator      Validator to add.
     * @param   array|null      $options        Vaidator options.
     * @return  ParameterInterface
     */
    public function addValidator(string $validator, ?array $options = null): ParameterInterface
    {
        if (is_null($this->validators)) {
            $this->validators = new ValidatorCollection();
        }
        $this->validators->add($validator, [$this->getName()], $options);
        return $this;
    }

    /**
     * Validate.
     * 
     * @return  string|null
     */
    public function validate(): ?string
    {
        if (!is_null($this->validators)) {
            $result = $this->validators->validate($this->getValue());
            if (false === $result) {
                return $this->validators->getFirstError();
            }
        }
        return null;
    }

    /**
     * Do we have conditionals?
     * 
     * @return  bool
     */
    public function hasConditionals(): bool
    {
        return (count($this->conditionals) > 0);
    }

    /**
     * Check conditionals.
     * 
     * @param   mixed   $command        Command value.
     * @param   mixed   $action         Action value.
     * @return  string|null
     */
    public function checkConditionals($command, $action): ?string
    {
        if (!$this->hasConditionals()) {
            return null;
        }

        foreach($this->conditionals as $cond) {
            if ($cond->match($command, $action)) {
                $result = $cond->check();
                if (!is_null($result)) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description.
     * 
     * @return  string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the flags.
     * 
     * @return  int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * Is this optional?
     * 
     * @return  bool
     */
    public function isOptional(): bool
    {
        return (self::OPTIONAL === ($this->flags & self::OPTIONAL));
    }

    /**
     * Is this compulsory?
     * 
     * @return  bool
     */
    public function isCompulsory(): bool
    {
        return (self::COMPULSORY === ($this->flags & self::COMPULSORY));
    }

    /**
     * Do we have choices?
     * 
     * @return  bool
     */
    public function hasChoices(): bool
    {
        return (!is_null($this->choices));
    }

    /**
     * Are choices relaxed?
     * 
     * @return  bool
     */
    public function isRelaxedChoices(): bool
    {
        return (self::RELAXCHOICES === ($this->flags & self::RELAXCHOICES));
    }

    /**
     * Get the choices.
     * 
     * @return  array|null
     */
    public function getChoices(): ?array
    {
        return $this->choices;
    }

    /**
     * See if we have a value.
     * 
     * @return  bool
     */
    public function hasValue(): bool
    {
        return !is_null($this->value);
    }

    /**
     * Get the value.
     * 
     * @return  mixed
     */
    public function getValue()
    {
        if (!is_null($this->value)) {
            return $this->value;
        }
        return $this->default;
    }

    /**
     * Set the value.
     * 
     * @param   mixed   $value  Value to set.
     * @return  ParameterInterface
     */
    public function setValue($value): ParameterInterface
    {
        $this->value = $value;
        return $this;
    }

    /**   
     * Get the default.
     * 
     * @return  string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Has an argument default?
     * 
     * @return  bool
     */
    public function hasDefault(): bool
    {
        return (!is_null($this->default));
    }
    
    /**
     * Is isgnored for help?
     * 
     * @return  bool
     */
    public function isIgnoredForHelp(): bool
    {
        return (self::HELPIGNORE === ($this->flags & self::HELPIGNORE));
    }
}
