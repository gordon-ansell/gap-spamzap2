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
use GreenFedora\Stdlib\Level;
use GreenFedora\Logger\Exception\RuntimeException;

/**
 * Class that's aware of the logger.
 */

trait LoggerAwareTrait
{
    /**
     * The actual logger.
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * Set the logger.
     * 
     * @param   LoggerInterface     $logger     Logger to set.
     * @return  void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger.
     * 
     * @return  LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if (is_null($this->logger)) {
            throw new RuntimeException("Logger is not set. Either pass it in the constructor or use setLogger().");
        }
        return $this->logger;
    }

    /**
     * System is unusable.
     *
     * @param string    $message
     * @param array     $context
     *
     * @return void
     */
    public function emergency($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::EMERGENCY, $message, $context, $section);
    }

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
    public function alert($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::ALERT, $message, $context, $section);
    }

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
    public function critical($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
           $this->getLogger()->log(Level::CRITICAL, $message, $context, $section);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function error($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::ERROR, $message, $context, $section);
    }

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
    public function warning($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::WARNING, $message, $context, $section);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function notice($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::NOTICE, $message, $context, $section);
    }

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
    public function info($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::INFO, $message, $context, $section);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function debug($message, $context = null, ?string $section = null)
    {
        if (!is_null($this->logger))
            $this->getLogger()->log(Level::DEBUG, $message, $context, $section);
    }

}
