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

/**
 * Base message interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface MessageInterface
{
    /**
     * Return the headers or get an individual header.
     * 
     * @param   string|null      $key       Key of header to get.
     * @param   mixed            $default   If header not there.
     * @return  mixed
     */
    public function header(?string $key = null, $default = null);

    /**
     * Set the body.
     *
     * @param   mixed       $content       Body content.
     * @return  ResponseInterface
     */
    public function setContent($content) : MessageInterface;

    /**
     * Get the content.
     * 
     * @return  mixed
     */
    public function getContent();

    /**
     * See if we have a header.
     * 
     * @param   string  $name   Header name.
     * @return  bool
     */
    public function hasHeader(string $name): bool;

    /**
     * Get a header.
     * 
     * @param   string  $name       Name of header to get.
     * @return  array
     */
    public function getHeader(string $name): array;

    /**
     * Get all the headers.
     * 
     * @return  array
     */
    public function getHeaders(): array;

    /**
     * Get a header line.
     * 
     * @param   string  $name       Name of header to get.
     * @return  string
     */
    public function getHeaderLine(string $name): string;

    /**
     * Set a header.
     * 
     * @param   string      $name       Name of header to set.
     * @param   string[]    $values     Array of values for this header.
     * @return  MessageInterface 
     */
    public function setHeader(string $name, array $values): MessageInterface;

    /**
     * Remove a header.
     * 
     * @param   string      $name       Name of header to set.
     * @return  MessageInterface 
     */
    public function removeHeader(string $name): MessageInterface;

    /**
     * Add a header line.
     * 
     * @param   string      $name       Name of header to set.
     * @param   string      $value      Value of header line.
     * @return  MessageInterface 
     */
    public function addHeaderLine(string $name, string $value): MessageInterface;

    /**
     * Create a version of ourself with a new header.
     * 
     * @param   string              $name       Name of header to replace.
     * @param   array|string        $values     Values for the header.
     * @return  MessageInterface
     */
    public function withHeader(string $name, $values): MessageInterface;

    /**
     * Create a version of ourself with a new header line added to a header.
     * 
     * @param   string              $name       Name of header to replace.
     * @param   array|string        $values     Values for the header.
     * @return  MessageInterface
     */
    public function withAddedHeader(string $name, $values): MessageInterface;

    /**
     * Create a version of ourself without a specific header.
     * 
     * @param   string              $name       Name of header to remove.
     * @return  MessageInterface
     */
    public function withoutHeader(string $name): MessageInterface;

    /**
     * Set the protocol.
     * 
     * @param   string           $protocol   Protocol.
     * @return  MessageInterface
     */
    public function setProtocol(string $protocol): MessageInterface;

    /**
     * Get the protocol version.
     * 
     * @return  string
     */
    public function getProtocolVersion(): string;

    /**
     * Create a clone with a new protocol version.
     * 
     * @param   string  $newVersion    New version.
     * @return  MessageInterface
     */
    public function withProtocolVersion(string $newVersion): MessageInterface;
}