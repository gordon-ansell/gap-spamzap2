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
namespace GreenFedora\Logger;

/**
 * Interface for the Logger class.
 */
interface LoggerInterface
{
    /**
     * Control the display of sections.
     * 
     * @param   mixed   $sections   Sections.
     * @return  LoggerInterface
     */
    public function displaySections($sections): LoggerInterface;

    /**
	 * Get or set the log level.
	 *
	 * @param 	mixed|null		$level		Log level to set or null for a fetch.
	 *
	 * @return 	int 		The current level if we fetch, otherwise the old level.
	 *
	 * @throws	InvalidArgumentException	If the log level is not allowed.
	 */
	public function level($level = null) : int;

	/**
	 * Get the message count for a level or levels.
	 *
	 * @param 	string		$level		Level or levels to get count for. Null returns all.			
	 *
	 * @return	int
	 */
	public function getMessageCount(string $level) : int;
	
	/**
	 * Get all the message counts.
	 *
	 * @return 	int[]
	 */
	public function getMessageCounts() : array;

    /**
     * Logs with an arbitrary level.
     *
     * @param 	mixed  	$level			Logging level.
     * @param 	string 	$message		Message string.
     * @param 	mixed  	$context		Additional context.
     * @param   string  $section        Section.
     * @return 	void
     */
    public function log($level, $message, $context = null, ?string $section = null);

    /**
     * System is unusable.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function emergency($message, $context = null, ?string $section = null);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function alert($message, $context = null, ?string $section = null);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function critical($message, $context = null, ?string $section = null);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function error($message, $context = null, ?string $section = null);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function warning($message, $context = null, ?string $section = null);

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function notice($message, $context = null, ?string $section = null);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function info($message, $context = null, ?string $section = null);

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed  $context
     *
     * @return void
     */
    public function debug($message, $context = null, ?string $section = null);

}
