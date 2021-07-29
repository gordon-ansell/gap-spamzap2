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

use GreenFedora\Console\Output\OutputInterface;

/**
 * Interface for the Command class.
 */
interface CommandInterface
{
    /**
     * Set the output.
     * 
     * @param   OutputInterface     $output     Output.
     * @return  CommandInterface
     */
    public function setOutput(OutputInterface $output): CommandInterface;

    /**
     * Initialisation.
     * 
     * @return  void
     */
    public function init();

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int;

}
