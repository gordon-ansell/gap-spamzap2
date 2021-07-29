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

use GreenFedora\Console\Output\ConsoleOutput;
use GreenFedora\GetOpt\Exception\OutOfBoundsException;
use GreenFedora\GetOpt\GetOptInterface;
use GreenFedora\GetOpt\Parameter;

/**
 * Command line options parser.
 */
class GetOpt implements GetOptInterface
{
    /**
     * Name.
     * @var string|null
     */
    protected $name = null;

    /**
     * Description.
     * @var string|null
     */
    protected $description = '';

    /**
     * Positional parameters.
     * @var PositionalInterface[]
     */
    protected $positionals = [];

    /**
     * Option parameters.
     * @var Option[]
     */
    protected $options = [];

    /**
     * Use command?
     * @var bool
     */
    protected $useCommand = true;

    /**
     * Use action?
     * @var bool
     */
    protected $useAction = true;

    /**
     * Constructor.
     * 
     * @param   bool    $useCommand     Use the command positional?
     * @param   bool    $useAction      Use the action positional?
     * @return  void
     */
    public function __construct(bool $useCommand = true, bool $useAction = true)
    {
        $this->useCommand = $useCommand;
        $this->useAction = $useAction;
        $this->output = new ConsoleOutput();
    }

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
    ?array $choices = null, $default = null): GetOptInterface
    {
        $this->positionals[$name] = new Positional($name, $description, $flags, $choices, $default);
        return $this;
    }

    /**
     * See if we have a positional parameter.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasPositional(string $name): bool
    {
        return array_key_exists($name, $this->positionals);
    }

    /**
     * Get a positional parameter.
     * 
     * @param   string      $name           Name to get.
     * @return  PositionalInterface
     * @throws  OutOfBoundsException
     */
    public function getPositional(string $name): PositionalInterface
    {
        if (!$this->hasPositional($name)) {
            throw new OutOfBoundsException(sprintf("No positional parameter with name '%s' found.", $name));
        }
        return $this->positionals[$name];
    }

    /**
     * Check we have a positional parameter at a given index.
     * 
     * @param   int         $index          Index to check.
     * @return  bool
     */
    public function hasPositionalIndex(int $index): bool
    {
        return ($index < count($this->positionals));
    }

    /**
     * Get a count of compulsory positional arguments.
     * 
     * @return  int
     */
    public function compulsoryPositionalCount(): int
    {
        $count = 0;
        foreach ($this->positionals as $item) {
            if (!$item->isOptional()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get a positional parameter by its index.
     * 
     * @param   int         $index          Index to get.
     * @return  PositionalInterface
     * @throws  OutOfBoundsException
     */
    public function getPositionalByIndex(int $index): PositionalInterface
    {
        if (!$this->hasPositionalIndex($index)) {
            throw new OutOfBoundsException(sprintf("No positional parameter at index %s.", $index));
        }
        return $this->positionals[array_keys($this->positionals)[$index]];
    }

    /**
     * See if we have a command.
     * 
     * @return  string|null
     */
    public function hasCommand(): ?string
    {
        if (!$this->useCommand) {
            return null;
        }
        foreach ($this->positionals as $name => $item) {
            if ($item->isCommand()) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Get the command value.
     * 
     * @return  mixed
     */
    public function getCommandValue(): ?string
    {
        $cmd = $this->hasCommand();
        if (is_null($cmd)) {
            return null;
        }
        return $this->getPositional($cmd)->getValue();
    }

    /**
     * See if we have an action.
     * 
     * @return  string|null
     */
    public function hasAction(): ?string
    {
        if (!$this->useAction) {
            return null;
        }
        foreach ($this->positionals as $name => $item) {
            if ($item->isAction()) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Get the action value.
     * 
     * @return  mixed
     */
    public function getActionValue(): ?string
    {
        $act = $this->hasAction();
        if (is_null($act)) {
            return null;
        }
        return $this->getPositional($act)->getValue();
    }

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
    ?string $argumentName = null, $default = null, ?array $choices = null): GetOptInterface
    {
        $this->options[$name] = new Option($name, $description, $flags, $shortcut, $argumentName, 
            $default, $choices);
        return $this;
    }

    /**
     * See if we have an option.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * See if we have an option value.
     * 
     * @param   string      $name           Name to check.
     * @return  bool
     */
    public function hasOptionValue(string $name): bool
    {
        return (array_key_exists($name, $this->options) and !is_null($this->getOption($name)->getValue()));
    }

    /**
     * Get an option.
     * 
     * @param   string      $name           Name to get.
     * @return  OptionInterface
     */
    public function getOption(string $name): OptionInterface
    {
        if (!$this->hasOption($name)) {
            throw new OutOfBoundsException(sprintf("No option with name '%s' found.", $name));
        }
        return $this->options[$name];
    }

    /**
     * Check if something is a valid shortcut.
     * 
     * @param   string      $test       Thing to test.
     * @return  string|null             Null if it isn't, otherwise the option it's a shortcut for.
     */
    public function isValidShortcut(string $test): ?string
    {
        foreach ($this->options as $name => $option) {
            if ($option->hasShortcut($test)) {
                return $name;
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
     * Set the name.
     * 
     * @param   string      $name       Name.
     * @return  GetOptInterface
     */
    public function setName(string $name): GetOptInterface
    {
        $this->name = $name;
        return $this;
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
     * Set the description.
     * 
     * @param   string      $desc       Description.
     * @return  GetOptInterface
     */
    public function setDescription(string $desc): GetOptInterface
    {
        $this->description = $desc;
        return $this;
    }

    /**
     * Split an option.
     * 
     * @param   string      $opt    Option to split.
     * @return  array               [name, value]
     */
    protected function splitOption(string $opt)
    {
        $name = '';
        $value = null;

        $opt = ltrim($opt, '-');
        $equalsPos = strpos($opt, '=');

        if (false !== $equalsPos) {
            $name = substr($opt, 0, $equalsPos);
            $value = substr($opt, $equalsPos + 1);
        } else {
            $name = $opt;
        }

        return [$name, $value];
    }

    /**
     * Parse arguments.
     * 
     * @param   array|null  $args       Arguments to parse.
     * @return  string|null             Null if all is well, otherwise an error message.
     */
    public function parseArgs(?array $args = null)
    {
        if (is_null($args)) {
            $args = $_SERVER['argv'];
        }

        // If we have no name, use the first argument.
        if (is_null($this->name)) {
            $this->name = $args[0];
        }

        // Let's lose the first option, which is the command line callable.
        array_shift($args);

        $positionals = [];
        $options = [];

        $lastOption = '';

        $processing = 'positionals';

        // Separate postitionals from options.
        foreach ($args as $index => $arg) {

            if ('--' === $arg) {

                if ('options' === $processing) {
                    $processing = 'positionals';
                    $lastOption = '';
                } else {
                    $processing = 'options';
                }

            } else if (str_starts_with($arg, '--')) {

                $processing = 'options';
                $name = substr($arg, 2);
                
                // NB.
                if ('help' === $name) {
                    $this->displayHelp();
                    return 'help';
                }

                $isRealValue = false;
                $value = true;
                $equalsPos = strpos($name, '=');
                if (false !== $equalsPos) {
                    $value = substr($name, $equalsPos + 1);
                    $name = substr($name, 0, $equalsPos);
                    $lastOption = '';
                    $isRealValue = true;
                } else {
                    $lastOption = $name;
                }

                if ($this->hasOption($name)) {
                    $options[$name] = $value;
                    $this->options[$name]->setIsRealValue($isRealValue);
                } else {
                    return sprintf("Invalid option '%s'.", $arg);
                }

            } else if (str_starts_with($arg, '-')) {

                $processing = 'options';
                $name = substr($arg, 1);

                // NB.
                if ('h' === $name) {
                    $this->displayHelp();
                    return 'help';
                }

                $isRealValue = false;
                $value = true;
                $equalsPos = strpos($name, '=');
                if (false !== $equalsPos) {
                    $value = substr($name, $equalsPos + 1);
                    $name = substr($name, 0, $equalsPos);
                    $lastOption = '';
                    $isRealValue = true;
                }

                if (strlen($name) > 1) {
                    if (false !== $equalsPos) {
                        return sprintf("Cannot assign arguments to compound options, '-%s'", $name);
                    } else {
                        for ($char = 0; $char < strlen($name); $char++) {

                            if ($this->hasOption($name[$char])) {
                                $options[$name[$char]] = $value;
                            } else if ($full = $this->isValidShortcut($name[$char])) {
                                $options[$full] = $value;
                            } else {
                                return sprintf("Invalid option '-%s'.", $name[$char]);
                            }

                        }
                        $lastOption = '';
                    }
                } else {

                    if ($this->hasOption($name)) {
                        $options[$name] = $value;
                        if ($this->hasOption($name)) {
                            $this->options[$name]->setIsRealValue($isRealValue);
                        }
                        if (false === $equalsPos) $lastOption = $name;
                    } else if ($full = $this->isValidShortcut($name)) {
                        if ($this->hasOption($full)) {
                            $this->options[$full]->setIsRealValue($isRealValue);
                        }
                        $options[$full] = $value;
                        if (false === $equalsPos) $lastOption = $full;
                    } else {
                        return sprintf("Invalid option '-%s'.", $name);
                    }
                }


            } else {

                if ('options' === $processing) {

                    if ($this->hasOption($lastOption) and $this->getOption($lastOption)->hasArgument() 
                    and '' !== $lastOption) {
                        $options[$lastOption] = $arg;
                        if ($this->hasOption($lastOption)) {
                            $this->options[$lastOption]->setIsRealValue();
                        }
                        $lastOption = '';
                    } else {
                        $processing = 'positionals';
                        $positionals[] = $arg;
                        $lastOption = '';
                    }

                } else {
                    $positionals[] = $arg;
                }
            }

        }
    
        //print_r($args);
        //print_r($positionals);
        //print_r($options);

        // Load in all the positionals.
        $positionalsCount = count($this->positionals);
        $count = 0;
        $arrayName = '';
        foreach ($this->positionals as $name => $item) {
            if (array_key_exists($count, $positionals)) {
                $item->setValue($positionals[$count]);
            } 

            if ($item->isArray()) {
                if ($count === $positionalsCount - 1) {
                    $arrayName = $name;
                } else {
                    return sprintf("Only the last positional parameter can be an array, not the '%s' parameter.", $name);
                }
            }
            $count++;
        }

        // Load an array value if we need to.
        if ('' !== $arrayName and count($positionals) > $count) {
            $stack = [];
            for ($i = $count; $i < count($positionals); $i++) {
                $stack[] = $positionals[$i];
            }
            array_unshift($stack, $this->getPositional($arrayName)->getValue());
            $this->getPositional($arrayName)->setValue($stack);
        }

        // Make sure we have a command.
        if ($this->useCommand) {
            if (count($this->positionals) > 0) {
                if (is_null($this->hasCommand())) {
                    for ($i = 0; $i < count($this->positionals); $i++) {
                        $item = $this->getPositionalByIndex($i);
                        if (!$item->isAction()) {
                            $item->setAsCommand();
                            break;
                        }
                    }
                }
            }
        }

        // Make sure we have an action.
        if ($this->useAction) {
            if (count($this->positionals) > 0) {
                if (is_null($this->hasAction())) {
                    for ($i = 0; $i < count($this->positionals); $i++) {
                        $item = $this->getPositionalByIndex($i);
                        if (!$item->isCommand()) {
                            $item->setAsAction();
                            break;
                        }
                    }
                }
            }
        }

        // Now grab the options.
        foreach ($this->options as $name => $item) {
            if (array_key_exists($name, $options)) {
                $this->getOption($name)->setValue($options[$name]);
            }
        }

        // Check the positionals.
        $vp = $this->validatePositionals();
        if (!is_null($vp)) return $vp;

        // Now validate the options.
        $vo = $this->validateOptions();
        if (!is_null($vo)) return $vo;

        // Good return.
        return null;

    }

    /**
     * Validate the positional parameters.
     * 
     * @return  string|null
     */
    public function validatePositionals(): ?string
    {
        // Check the positionals.
        foreach ($this->positionals as $name => $item) {

            if ($item->isCompulsory() and is_null($item->getValue())) {
                return sprintf("You must specify a value for the '%s' parameter.", $name);
            }

            if ($item->hasChoices() and !$item->isRelaxedChoices()) {
                if (!in_array($item->getValue(), $item->getChoices())) {
                    return sprintf("The '%s' parameter must be one of '%s'.", $name, implode(', ', $item->getChoices()));
                }
            }

            $v = $item->validate();
            if (!is_null($v)) {
                return $v;
            }

            if ($item->hasConditionals()) {
                $result = $item->checkConditionals($this->getCommandValue(), $this->getActionValue());
                if (!is_null($result)) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Validate the option parameters.
     * 
     * @return  string|null
     */
    public function validateOptions(): ?string
    {
        foreach ($this->options as $name => $item) {
            
            if (is_bool($this->getOption($name)) and $this->getOption($name)->isCompulsory()) {
                return sprintf("Option '%s' must have a value.", $name);
            }

            if ($item->hasChoices()) {
                if (!in_array($item->getValue(), $item->getChoices())) {
                    return sprintf("The '%s' option must be one of '%s'.", $name, implode(', ', $item->getChoices()));
                }
            }

            $v = $item->validate();
            if (!is_null($v)) {
                return $v;
            }

            if ($item->hasConditionals()) {
                $result = $item->checkConditionals($this->getCommandValue(), $this->getActionValue());
                if (!is_null($result)) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Display the help for this command.
     * 
     * @param   string  $appName    Application name.
     * @return  void
     */
    public function displayHelp(?string $appName = null): void
    {
        $this->output->blank();
        $this->output->writeln($this->getHelpIntro($appName));
        $this->output->blank();
        $this->output->writeln($this->getDescription());
        $this->output->blank();
        $this->output->writeln('Usage:');
        $this->output->writeln($this->getHelpFormatLine($appName));

        $options = $this->getHelpOptions();
        if (count($options) > 0) {
            $this->output->blank();
            $this->output->writeln("Options:");
            foreach ($options as $line) {
                $this->output->writeln($line);
            }
        }

        $this->output->blank();
    }

    /**
     * Get the help intro.
     * 
     * @param   string  $appName    Application name.
     * @return  string
     */
    public function getHelpIntro(?string $appName = null): string
    {
        if (is_null($appName)) {
            $appName = app()->singleton('input')->getAppName();
        }
        return str_replace('./', '', $appName) . ' ' . $this->getName() . ':';
    }

    /**
     * Get the help format line.
     * 
     * @param   string      $appName        Application name.
     * @param   bool        $includeHelp    Include the help option?
     * @param   int         $indent         Indenting.
     * @return  string
     */
    public function getHelpFormatLine(?string $appName = null, bool $includeHelp = true, int $indent = 3): string
    {
        if (is_null($appName)) {
            $appName = app()->singleton('input')->getAppName();
        }
        $ret = str_repeat(' ' , $indent) 
            . str_replace('./', '', $appName) 
            . ' ' . $this->getName();

        foreach ($this->positionals as $k => $v) {
            if ($v->isIgnoredForHelp()) {
                continue;
            }
            if (!$v->hasChoices()) {
                $arg = '<' . $k . '>';
            } else {
                $c = '';
                foreach($v->getChoices() as $choice) {
                    if ('' !== $c) $c .= '|';
                    $c .= $choice;
                }
                $arg = '<' . $c . '>';
            }
            if ($v->isArray()) {
                 $arg .= '...';
            }
            if (!$v->isCompulsory()) {
                $arg = '[' . $arg . ']';
            }
            if ('' != $ret) $ret .= ' ';
            $ret .= $arg;
        }

        foreach ($this->options as $k => $v) {

            if (('help' == $k or 'h' == $k) and !$includeHelp) {
                continue;
            }

            if (1 == strlen($k)) {
                $opt = '-' . $k;
            } else {
                $opt = '--' . $k;
            }

            $optval = '';
            if ($v->hasArgumentName()) {
                $optval = '=<';
                $optval .= $v->getArgumentName();
                $optval .= '>';
            }

            $opt .= $optval;

            if ($v->hasShortcut()) {
                $opt .= ' | -' . $v->getShortcut() . $optval;
            }

            if (!$v->isCompulsory()) {
                $opt = '[' . $opt . ']';
            }

            if ('' != $ret) $ret .= ' ';
            $ret .= $opt;
        }

        return $ret;
    }

    /**
     * Get the help options bit.
     * 
     * @param   int         $indent     Indenting.
     * @return  array
     */
    public function getHelpOptions(int $indent = 3): array
    {
        $ret = [];

        $longest = 0;

        // First parse.
        if (count($this->options) > 0) {
            foreach ($this->options as $k => $v) {
                $opt = '';
                if (1 == strlen($k)) {
                    $opt = '-' . $k;
                } else {
                    $opt = '--' . $k;
                }    

                $optval = '';
                if ($v->hasArgumentName()) {
                    $optval = '=<';
                    $optval .= $v->getArgumentName();
                    $optval .= '>';
                }
    
                $opt .= $optval;
    
                if ($v->hasShortcut()) {
                    $opt .= ' -' . $v->getShortcut() . $optval;
                }

                $opt = str_repeat(' ', $indent) . $opt;

                if (strlen($opt) > $longest) {
                    $longest = strlen($opt);
                }

                $ret[] = $opt;
            }
        }

        // Second parse.
        $lineno = 0;
        if (count($this->options) > 0) {
            foreach ($this->options as $k => $v) {

                $ret[$lineno] = str_pad($ret[$lineno], $longest + 1);

                $ret[$lineno] .= ' ' . $v->getDescription();

                if ($v->hasDefault() and false !== $v->getDefault()) {
                    $ret[$lineno] .= ' [default: ' . $v->getDefault() . ']';
                }

                $lineno++;
            }
        }

        return $ret;
    }
}
