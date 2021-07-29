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

use GreenFedora\Application\ApplicationInterface;
use GreenFedora\Console\Input\InputInterface;
use GreenFedora\Console\Output\OutputInterface;

/**
 * Interface for the console application.
 */
interface ConsoleApplicationInterface extends ApplicationInterface
{
    /**
     * Run stuff.
     * 
     * @param   InputInterface      $input      Input.
     * @param   OutputInterface     $output     Output
     * @return
     */
    public function run(InputInterface $input, ?OutputInterface $output = null);
}
