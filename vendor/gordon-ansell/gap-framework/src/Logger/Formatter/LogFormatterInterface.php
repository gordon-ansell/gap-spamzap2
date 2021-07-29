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
namespace GreenFedora\Logger\Formatter;

/**
 * Log formatter interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface LogFormatterInterface
{
	/**
	 * Format the message.
	 *
	 * @param 	string 				$msg 		Message to format.
	 * @param 	int 				$level 		Level of message.
	 * @param 	mixed 				$context	Message context.
	 * @param 	mixed 				$section 	Section.
	 *
	 * @return 	string
	 */	
	public function format(string $msg, int $level, $context = null, ?string $section = null) : string;
}