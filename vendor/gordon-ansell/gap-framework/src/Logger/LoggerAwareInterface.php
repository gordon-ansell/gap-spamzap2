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

use GreenFedora\Logger\LoggerInterface;

/**
 * Class that's aware of the logger.
 */

interface LoggerAwareInterface
{
    /**
     * Set the logger.
     * 
     * @param   LoggerInterface     $logger     Logger to set.
     * @return  void
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Get the logger.
     * 
     * @return  LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * System is unusable.
     *
     * @param string    $message
     * @param array     $context
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
     * @param mixed $context
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
     * @param mixed $context
     *
     * @return void
     */
    public function critical($message, $context = null, ?string $section = null);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed $context
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
     * @param mixed $context
     *
     * @return void
     */
    public function warning($message, $context = null, ?string $section = null);

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed $context
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
     * @param mixed $context
     *
     * @return void
     */
    public function info($message, $context = null, ?string $section = null);

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function debug($message, $context = null, ?string $section = null);

}
