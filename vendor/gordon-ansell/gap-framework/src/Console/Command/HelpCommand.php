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

use GreenFedora\Application\ApplicationInterface;
use GreenFedora\Console\Command\Command;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Console\ConsoleKernel;
use GreenFedora\Logger\LoggerInterface;

/**
 * A console help command.
 */
class HelpCommand extends Command
{
    /**
     * Command name.
     * @var string|null
     */
    protected $name = 'help';

    /**
     * Command description.
     * @var string|null
     */
    protected $description = 'Show the help.';

    /**
     * Save the kernel.
     * @var ConsoleKernel
     */
    protected $kernel = null;

    /**
     * Constructor.
     * 
     * @param   ConsoleKernel           $kernel         The kernel.
     * @param   OutputInterface         $output         Output.
     * @param   LoggerInterface         $logger         Logger.
     * @return  void
     */
    public function __construct(ConsoleKernel $kernel, ?OutputInterface $output = null, LoggerInterface $logger = null)
    {
        $this->kernel = $kernel;
        parent::__construct($output, $logger);
    }

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int
    {
        $this->output->blank();
        $this->output->writeln(str_replace('./', '', app()->singleton('input')->getAppName()) . ':');

        if (count($this->kernel->getCommands()) > 0) {

            $this->output->blank();
            $this->output->writeln('Usage:');

            foreach ($this->kernel->getCommands() as $k => $v) {
                $this->output->writeln($v->getHelpFormatLine(null, false));
            }
        }

        $this->output->blank();
        return 0;
    }

}
