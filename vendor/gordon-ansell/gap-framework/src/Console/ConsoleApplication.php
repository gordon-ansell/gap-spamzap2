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

use GreenFedora\Application\Application;
use GreenFedora\Console\Input\InputInterface;
use GreenFedora\Console\Input\ArgvInput;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Console\ConsoleApplicationInterface;
use GreenFedora\Console\Output\ConsoleOutput;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Logger\Logger;
use GreenFedora\Logger\Formatter\StdLogFormatter;
use GreenFedora\Logger\Writer\ConsoleLogWriter;
use GreenFedora\Logger\Writer\FileLogWriter;

/**
 * Console application.
 */
class ConsoleApplication extends Application implements ConsoleApplicationInterface
{
    /**
     * Configure a standard logger.
     * 
     * @return  LoggerInterface
     */
    public function configureStandardLogger(): LoggerInterface
    {
        $config = $this->getConfig('logger');        
        $formatter = new StdLogFormatter($config);
        $writers = [
            new ConsoleLogWriter($config, $formatter), 
            new FileLogWriter($config, $formatter)
        ];
        $this->registerSingleton('logger', Logger::class, [null, $writers]);
        $logger = $this->get('logger');
        if ($this->hasConfig('env.LOG_LEVEL')) {
            $logger->level($this->getConfig('env.LOG_LEVEL'));
        }
        $this->setLogger($logger);
        return $logger;
    }

    /**
     * Run stuff.
     * 
     * @param   InputInterface|null      $input      Input.
     * @param   OutputInterface|null     $output     Output
     * @return  int     
     */
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        // Create the input handler if necessary.
        if (is_null($input)) {
            $input = new ArgvInput();
        }

        // Create the output handler if necessary.
        if (is_null($output)) {
            $output = new ConsoleOutput();
        }

        // Save the input and output.
        $this->registerSingletonInstance('input', $input);
        $this->registerSingletonInstance('output', $output);

        // Get the command name from the input.
        $commandName = $input->getCommandName();

        // Error if there isn't one.
        if (is_null($commandName)) {
            $output->critical("No command argument passed at command line.");
            return 1;
        }

        // See if the command exists.
        if (!$this->singleton('kernel')->hasCommand($commandName)) {
            $output->critical(sprintf("Command '%s' not found.", $commandName));
            return 1;
        }

        // Grab the command and parse the args into it.
        $ret = $this->singleton('kernel')->getCommand($commandName)
            ->setOutput($output)
            ->parseArgs($input->getArgs());

        // Help?
        if ('help' === $ret) {
            return 0;
        }

        // If that failed, just return.
        if (!is_null($ret)) {
            $this->output->error($ret);
            return 1;
        }

        // Execute.
        $ret = $this->singleton('kernel')->getCommand($commandName)->execute();

        return $ret;
    }
}
