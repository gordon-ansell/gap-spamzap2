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
namespace GreenFedora\Console;

use GreenFedora\Console\ConsoleApplicationInterface;
use GreenFedora\Stdlib\Path;
use GreenFedora\Console\Command\Command;
use GreenFedora\Console\Command\HelpCommand;
use GreenFedora\Console\Command\CommandInterface;
use GreenFedora\Console\Exception\RuntimeException;
use GreenFedora\Console\Input\InputInterface;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Finder\Finder;
use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;

/**
 * The kernel that drives console applications.
 */
class ConsoleKernel implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Application instance.
     * @var ConsoleApplicationInterface
     */
    protected $app = null;

    /**
     * Commands.
     * @var CommandInterface[]
     */
    protected $commands = [];

    /**
     * Constructor.
     * 
     * @param   ConsoleApplicationInterface     $app        Application instance.
     * @param   LoggerInterface                 $logger     Logger.
     * @return  void
     */
    public function __construct(ConsoleApplicationInterface $app, LoggerInterface $logger = null)
    {
        $this->app = $app;
        if (!is_null($logger)) {
            $this->setLogger($logger);
        }
    }

    /**
     * Initialisation.
     *
     * @return  void
     */
    public function init(): void
    {

    }

    /**
     * Get all the commands.
     * 
     * @return  CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Add a command.
     * 
     * @param   string              $name       Command name.
     * @param   CommandInterface    $command    Command to add.
     * @return  void
     */
    public function addCommand(string $name, CommandInterface $command): void
    {
        $this->commands[$name] = $command;
    }

    /**
     * Get a command.
     * 
     * @param   string  $name   Command name.
     * 
     * @return  CommandInterface
     */
    public function getCommand(string $name): CommandInterface
    {
        if ($this->hasCommand($name)) {
            return $this->commands[$name];
        }
        throw new RuntimeException(sprintf("Command '%s' not found.", $name));
    }

    /**
     * See if we have a command.
     * 
     * @param   string  $name   Command name.
     * 
     * @return  bool
     */
    public function hasCommand(string $name): bool
    {
        return array_key_exists($name, $this->commands);
    }

    /**
     * Dispatch the input.
     * 
     * @param   InputInterface|null      $input      Input.
     * @param   OutputInterface|null     $output     Output
     * @return  int
     */
    public function dispatch(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        try {
            return $this->app->run($input, $output);

        } catch (\Throwable $ex) {
            $this->alert("Exception encountered dispatching input from the kernel.", $ex);
        }
        return 1;
    }

    /**
     * Load up the console commands.
     * 
     * @param   string|array|null    $paths         Paths to look in.
     * 
     * @return  void
     * 
     * @throws  RuntimeException
     */
    public function load($paths = null)
    {
        $this->debug('Loading console commands', null, __METHOD__);

        if (is_null($paths)) {
            $paths = Path::join($this->app->getAppPath(), 'Console', 'Command');
        }

        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $appns = $this->app->getNamespace();

        foreach ($paths as $path) {
            foreach ((new Finder($path))->filter(true) as $file) {

                $commandClass = Path::nsjoin($appns, 'Console', 'Command', str_replace('.php', '', $file->getFilename()));

                $reflection = new \ReflectionClass($commandClass);

                if ($reflection->isSubclassOf(Command::class) and !$reflection->isAbstract()) {
                    $registerName = strtolower('command_' . str_replace('.php', '', $file->getFilename()));
                    $this->app->registerSingleton($registerName, $reflection->getName(), 
                        [null, $this->getLogger(), $this->app]);
                    $command = $this->app->singleton($registerName);
                    $command->init();
                    if (is_null($command->getName())) {
                        throw new RuntimeException(sprintf("No name set in init for '%s' command class.", $commandClass));
                    }
                    $this->commands[$command->getName()] = $command;
                }
            }
        }

        // Add a help command?
        if (!array_key_exists('help', $this->commands)) {
            $this->app->registerSingleton('command_helpcommand', HelpCommand::class, [$this, null, $this->getLogger()]);
            $this->commands['help'] = $this->app->singleton('command_helpcommand');
        }
    }
}
