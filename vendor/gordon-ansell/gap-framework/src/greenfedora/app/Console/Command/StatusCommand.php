<?php
/**
 * This file is part of the Gf package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Gf\Console\Command;

use GreenFedora\Console\Command\Command;
use GreenFedora\Container\Container;

/**
 * Status command.
 */
class StatusCommand extends Command
{   
    /**
     * Initialisation.
     * 
     * @return void
     */
    public function init()
    {
        $this->setName('status')
            ->setDescription('Status of the dependency injection container.')
            ->addOption('help', 'Show this help.', null, 'h');
    }

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int
    {
        $d = Container::getInstance()->dump();
        $this->output->blank();
        foreach ($d as $line) {
            $this->output->writeln($line);
        }
        $this->output->blank();
        return 0;
    }
}
