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
namespace GreenFedora\Http;

use GreenFedora\Uri\UriInterface;
use GreenFedora\Message\RequestInterface;

/**
 * HTTP request interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface HttpRequestInterface extends RequestInterface
{
    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @return string|resource The request body content or a resource to read the body stream
     */
    public function constructContent(bool $asResource = false);

    /**
     * Get the request URI.
     *
     * @return  UriInterface
     */
    public function getRequestUri() : UriInterface;

    /**
     * Get the base URI.
     *
     * @return  string
     */
    public function getBaseUri() : string;

    /**
     * Get the request method.
     *
     * @return  string
     */
    public function getMethod() : string;

    /**
     * See if this is a POST request.
     *
     * @return  bool
     */
    public function isPost() : bool;

    /**
     * Set the request as dispatched.
     *
     * @param   bool            $flag       Flag to set.
     * @return  self
     */
    public function setDispatched(bool $flag = true) : self;

    /**
     * Have we been dispatched?
     *
     * @return  bool
     */
    public function isDispatched() : bool;

    /**
     * Get get variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function get(?string $key = null, $default = null);

    /**
     * Get post variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function post(?string $key = null, $default = null);

    /**
     * See if we have a post variable.
     *
     * @param   string          $key        Key to get.
     * @return  bool
     */
    public function hasPost(string $key) : bool;

    /**
     * Check the form submitted.
     *
     * @param   string  $form   Form name to check.
     * @return  bool
     */
    public function formSubmitted(string $form): bool;

    /**
     * Get request variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function request(?string $key = null, $default = null);

    /**
     * Get server variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function server(?string $key = null, $default = null);

    /**
     * Get cookie variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function cookie(?string $key = null, $default = null);

    /**
     * Get files variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function files(?string $key = null, $default = null);

}

