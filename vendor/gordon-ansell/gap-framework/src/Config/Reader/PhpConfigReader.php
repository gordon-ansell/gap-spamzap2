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
namespace GreenFedora\Config\Reader;

/**
 * Reads PHP config files.
 */
class PhpConfigReader implements ConfigReaderInterface
{
	/**
	 * Read the file and return the results as an array.
	 *
	 * @param 	string 		$file		File to read.
	 *
	 * @return	array
	 */
	public function read(string $file) : array
	{
        return include $file;
	}	
}
