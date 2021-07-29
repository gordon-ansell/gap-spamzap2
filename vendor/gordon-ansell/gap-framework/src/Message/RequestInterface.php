<?php

/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Message;

use GreenFedora\Message\MessageInterface;

/**
 * Base request interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface RequestInterface extends MessageInterface
{
    /**
     * Get the route info.
     *
     * @return  string|null
     */
    public function getRoute() : ?string;

	/**
	 * Get the command line arguments.
	 *
	 * @return	array
	 */
	public function getArgs(): array;

	/**
	 * Get an argument.
	 *
	 * For arguments that are just present but without a value (switches) we return true.
	 * Otherwise we return the value.
	 *
	 * @param 	string 		$name		Argument name.
	 * @param 	mixed 		$default 	Default if arg not found.
	 * @return	mixed
	 *
	 * @throws 	OutOfBoundsException 	If argument not found and no default.
	 */
	public function getArg(string $name, $default = null);

    /**
	 * See if we have a particular argument.
	 *
	 * @param 	string 		$name 		Argument name.
	 *
	 * @return 	bool
	 */
	public function hasArg(string $name) : bool;
}