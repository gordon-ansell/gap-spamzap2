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
namespace GreenFedora\Config;

use GreenFedora\Stdlib\Arr\DottedArrInterface;

/**
 * Interface for the Config class.
 */
interface ConfigInterface extends DottedArrInterface
{
	/**
	 * Set the base path.
	 * 
	 * @param 	string 	$basePath 	Base path to set.
	 * @return 	ConfigInterface
	 */
	public function setBasePath(string $basePath): ConfigInterface;

	/**
	 * Process passed paths for configs.
	 *
	 * Any files in the path are potential config files and we determine what type by their extension:
	 *
	 *	-	.php indicates a PHP config file that will simply be 'required'.
	 *
	 * Subdirectories on each path can be named 'dev' or 'test' and are only processed if the given mode
	 * is passed in.
	 *
	 * Paths are processed in the order they're passed in with later values overwriting older ones. However,
	 * if the mode is 'dev' or 'test' then these subdirectories are processed after the main directory.
	 *
	 * @param 	string|null 		$mode 		The run mode, which will be 'dev', 'test' or 'prod'.
	 * @param 	array|string|null	$paths		A single path string, an array of paths or null just to use the base app path.
	 *
	 * @return	ConfigInterface
	 */
	public function process(?string $mode = 'prod', $paths = null): ConfigInterface;
}
