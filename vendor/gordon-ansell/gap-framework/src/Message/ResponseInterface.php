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
 * Base output interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface ResponseInterface extends MessageInterface
{
    /**
     * Set the status code.
     *
     * @param   int         $code       Code to set.
     * @return  self
     */
    public function setStatusCode(int $code) : self;

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode() : int;

    /**
     * See if we can send headers.
     *
     * @param   bool    $throw      Throw exception if sent?
     * @return  bool
     * @throws  HeadersSentException
     */
    public function canSendHeaders(bool $throw = false) : bool;

    /**
     * Send the headers.
     *
     * @return  self
     */
    public function sendHeaders() : self;

    /**
     * Send the body.
     *
     * @return  self
     */
    public function sendContent() : self;

    /**
     * Send the request.
     *
     * @return void
     */
    public function send();

    /**
     * Add an exception to the response.
     *
     * @param   \Exception    $exception      Exception to add.
     * @return  self
     */
    public function addException(\Exception $exception) : self;

    /**
     * Do we have exceptions?
     *
     * @return bool
     */
    public function hasExceptions() : bool;
}