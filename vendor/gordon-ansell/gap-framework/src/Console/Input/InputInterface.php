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

/**
 * Interface for the Input class.
 */
interface InputInterface
{
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
