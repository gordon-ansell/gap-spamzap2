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

/**
 * Interface for a command line parameter.
 */
interface ParameterInterface
{
    /**
     * Add a validator.
     * 
     * @param   string          $validator      Validator to add.
     * @param   array|null      $options        Vaidator options.
     * @return  ParameterInterface
     */
    public function addValidator(string $validator, ?array $options = null): ParameterInterface;

    /**
     * Validate.
     * 
     * @return  string|null
     */
    public function validate(): ?string;

    /**
     * Do we have conditionals?
     * 
     * @return  bool
     */
    public function hasConditionals(): bool;

    /**
     * Check conditionals.
     * 
     * @param   mixed   $command        Command value.
     * @param   mixed   $action         Action value.
     * @return  string|null
     */
    public function checkConditionals($command, $action): ?string;

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string;

    /**
     * Get the description.
     * 
     * @return  string
     */
    public function getDescription(): string;

    /**
     * Get the flags.
     * 
     * @return  int
     */
    public function getFlags(): int;

    /**
     * Is this optional?
     * 
     * @return  bool
     */
    public function isOptional(): bool;

    /**
     * Is this compulsory?
     * 
     * @return  bool
     */
    public function isCompulsory(): bool;

    /**
     * Do we have choices?
     * 
     * @return  bool
     */
    public function hasChoices(): bool;

    /**
     * Are choices relaxed?
     * 
     * @return  bool
     */
    public function isRelaxedChoices(): bool;
    
    /**
     * Get the choices.
     * 
     * @return  array|null
     */
    public function getChoices(): ?array;

    /**
     * See if we have a value.
     * 
     * @return  bool
     */
    public function hasValue(): bool;

    /**
     * Get the value.
     * 
     * @return  mixed
     */
    public function getValue();

    /**
     * Set the value.
     * 
     * @param   mixed   $value  Value to set.
     * @return  ParameterInterface
     */
    public function setValue($value): ParameterInterface;
    
    /**
     * Get the default.
     * 
     * @return  string|null
     */
    public function getDefault(): ?string;

    /**
     * Has a default?
     * 
     * @return  bool
     */
    public function hasDefault(): bool;
    
    /**
     * Is isgnored for help?
     * 
     * @return  bool
     */
    public function isIgnoredForHelp(): bool;
}
