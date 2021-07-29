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
namespace GreenFedora\Logger\Writer;

use GreenFedora\Logger\Writer\Exception\RuntimeException;

/**
 * Interface for the LogWriter class.
 */
interface LogWriterInterface
{
	/**
	 * Write a log message.
	 *
	 * @param 	string 				$msg 		Message to write.
	 * @param 	int 				$level 		Level of message.
	 * @param 	mixed 				$context	Message context.
	 * @param 	mixed 				$section 	Section.
	 *
	 * @return 	void
	 *
	 * @throws 	RuntimeException 	If we have any problems at all.
	 */
	public function write(string $msg, int $level, $context = null, ?string $section = null);
}
