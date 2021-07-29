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
namespace GreenFedora\Console\Command;

use GreenFedora\Console\ConsoleApplicationInterface;
use GreenFedora\Console\Command\CommandInterface;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;

use GreenFedora\GetOpt\GetOpt;

/**
 * A console command.
 */
class Command extends GetOpt implements CommandInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Output.
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * Parent app.
     * @var ConsoleApplicationInterface
     */
    protected $app = null;

    /**
     * Constructor.
     * 
     * @param   OutputInterface                 $output     Output.
     * @param   LoggerInterface                 $logger     Logger.
     * @param   ConsoleApplicationInterface     $app        Parent application.
     * @return  void
     */
    public function __construct(?OutputInterface $output = null, LoggerInterface $logger = null, 
        ConsoleApplicationInterface $app = null)
    {
        $this->output = $output;
        if (!is_null($logger)) {
            $this->setLogger($logger);
        }
        $this->app = $app;
    }

    /**
     * Get the application.
     * 
     * @return  ConsoleApplicationInterface
     */
    public function getApp(): ConsoleApplicationInterface
    {
        return $this->app;
    }

    /**
     * Set the output.
     * 
     * @param   OutputInterface     $output     Output.
     * @return  CommandInterface
     */
    public function setOutput(OutputInterface $output): CommandInterface
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Initialisation.
     * 
     * @return  void
     */
    public function init()
    {
    }

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int
    {
        return 0;
    }


}
