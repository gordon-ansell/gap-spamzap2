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

use GreenFedora\Console\Input\InputInterface;

/**
 * Argv console input interface.
 */
interface ArgvInputInterface extends InputInterface
{
    /**
     * Get the app name.
     * 
     * @return string
     */
    public function getAppName(): string;

    /**
     * Get the command.
     * 
     * @return string
     */
    public function getCommandName(): ?string;

    /**
     * Get the arguments.
     * 
     * @return  array
     */
    public function getArgs(): array;
}
