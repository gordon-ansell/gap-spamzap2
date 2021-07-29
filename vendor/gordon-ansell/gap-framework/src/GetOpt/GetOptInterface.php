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
 * Interface for the GetOpt class.
 */
interface GetOptInterface
{
    /**
     * Add a positional parameter.
     * 
     * @param   string      $name           Argument name.
     * @param   string      $description    Description.
     * @param   int|null    $flags          Flags.
     * @param   array|null  $choices        Choices.
     * @param   mixed       $default        Default.
     * @return  GetOptInterface
     */
    public function addPositional(string $name, string $description, ?int $flags = Parameter::COMPULSORY, 
    ?array $choices = null, $default = null): GetOptInterface;

    /**
     * See if we have a positional parameter.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasPositional(string $name): bool;

    /**
     * Get a positional parameter.
     * 
     * @param   string      $name           Name to get.
     * @return  PositionalInterface
     * @throws  OutOfBoundsException
     */
    public function getPositional(string $name): PositionalInterface;

    /**
     * Check we have a positional parameter at a given index.
     * 
     * @param   int         $index          Index to check.
     * @return  bool
     */
    public function hasPositionalIndex(int $index): bool;

    /**
     * Get a count of compulsory positional arguments.
     * 
     * @return  int
     */
    public function compulsoryPositionalCount(): int;

    /**
     * Get a positional parameter by its index.
     * 
     * @param   int         $index          Index to get.
     * @return  PositionalInterface
     * @throws  OutOfBoundsException
     */
    public function getPositionalByIndex(int $index): PositionalInterface;

    /**
     * See if we have a command.
     * 
     * @return  string|null
     */
    public function hasCommand(): ?string;

    /**
     * Get the command value.
     * 
     * @return  mixed
     */
    public function getCommandValue(): ?string;

    /**
     * See if we have an action.
     * 
     * @return  string|null
     */
    public function hasAction(): ?string;

    /**
     * Get the action value.
     * 
     * @return  mixed
     */
    public function getActionValue(): ?string;

    /**
     * Add a option parameter.
     * 
     * @param   string          $name               Argument name.
     * @param   string          $description        Description.
     * @param   int|null        $flags              Flags.
     * @param   string|null     $shortcut           Shortcut.
     * @param   string|null     $argumentName       Argument name.
     * @param   mixed           default             Argument default.  
     * @param   array|null      $choices            Choices.
     * @return  GetOptInterface
     */
    public function addOption(string $name, string $description, ?int $flags = Parameter::OPTIONAL, ?string $shortcut = null,
    ?string $argumentName = null, $default = null, ?array $choices = null): GetOptInterface;

    /**
     * See if we have an option.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasOption(string $name): bool;

    /**
     * See if we have an option value.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasOptionValue(string $name): bool;

    /**
     * Get an option.
     * 
     * @param   string      $name           Name to get.
     * @return  OptionInterface
     */
    public function getOption(string $name): OptionInterface;

    /**
     * Check if something is a valid shortcut.
     * 
     * @param   string      $test       Thing to test.
     * @return  string|null             Null if it isn't, otherwise the option it's a shortcut for.
     */
    public function isValidShortcut(string $test): ?string;

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string;

    /**
     * Set the name.
     * 
     * @param   string      $name       Name.
     * @return  GetOptInterface
     */
    public function setName(string $name): GetOptInterface;

    /**
     * Get the description.
     * 
     * @return  string
     */
    public function getDescription(): string;

    /**
     * Set the description.
     * 
     * @param   string      $desc       Description.
     * @return  GetOptInterface
     */
    public function setDescription(string $desc): GetOptInterface;

    /**
     * Parse arguments.
     * 
     * @param   array|null  $args       Arguments to parse.
     * @return  string|null             Null if all is well, otherwise an error message.
     */
    public function parseArgs(?array $args = null);

    /**
     * Validate the positional parameters.
     * 
     * @return  string|null
     */
    public function validatePositionals(): ?string;

    /**
     * Validate the option parameters.
     * 
     * @return  string|null
     */
    public function validateOptions(): ?string;

    /**
     * Display the help for this command.
     * 
     * @param   string  $appName    Application name.
     * @return  void
     */
    public function displayHelp(?string $appName = null): void;

    /**
     * Get the help intro.
     * 
     * @param   string  $appName    Application name.
     * @return  string
     */
    public function getHelpIntro(?string $appName = null): string;

    /**
     * Get the help format line.
     * 
     * @param   string      $appName        Application name.
     * @param   bool        $includeHelp    Include the help option?
     * @param   int         $indent         Indenting.
     * @return  string
     */
    public function getHelpFormatLine(?string $appName = null, bool $includeHelp = true, int $indent = 3): string;

    /**
     * Get the help options bit.
     * 
     * @param   int         $indent     Indenting.
     * @return  array
     */
    public function getHelpOptions(int $indent = 3): array;
}
