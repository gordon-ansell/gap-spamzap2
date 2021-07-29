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
namespace GreenFedora\Console\Input;

use GreenFedora\Console\Input\Input;
use GreenFedora\Console\Input\ArgvInputInterface;

/**
 * Argv console input.
 */
class ArgvInput extends Input implements ArgvInputInterface
{
    /**
     * Raw input.
     * @var array
     */
    protected $raw = [];

    /**
     * App name.
     * @var string
     */
    protected $appName = '';

    /**
     * Args left after we've parsed the command.
     * @var array
     */
    protected $args = [];

    /**
     * Command name.
     * @var string|null
     */
    protected $commandName = null;

    /**
     * Constructor.
     * 
     * @param   array|null   $argv   The input.
     * @return  void
     */
    public function __construct(?array $argv = null)
    {
        if (is_null($argv)) {
            $argv = $_SERVER['argv'];
        }
        $this->raw = $argv;

        $this->parseCommandName();
    }

    /**
     * Create from the environment.
     * 
     * @return ArgvInput
     */
    public static function createFromEnvironment(): ArgvInputInterface
    {
        return new self($_SERVER['argv']);
    }

    /**
     * Parse the command name.
     * 
     * @return  void
     */
    protected function parseCommandName(): void
    {
        $args = $this->raw;
        $this->appName = $args[0];
        $this->commandName = $args[1];
        $this->args = $args;
    }

    /**
     * Get the app name.
     * 
     * @return string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Get the command.
     * 
     * @return string
     */
    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * Get the arguments.
     * 
     * @return  array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

}
