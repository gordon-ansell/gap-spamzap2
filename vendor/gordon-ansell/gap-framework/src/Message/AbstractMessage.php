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
use GreenFedora\Stdlib\Arr\Arr;

/**
 * Base message.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

abstract class AbstractMessage implements MessageInterface
{	
    /**
     * Protocol.
     * @var string
     */
    protected $protocol = null;

    /**
     * Headers.
     * @var Arr
     */
    protected $headers = null;

    /**
     * Content.
     * @var mixed
     */
    protected $content = '';

    /**
     * Constructor.
     * 
     * @param   mixed           $content     Content.
     * @param   iterable        $headers     Headers.    
     * @param   string|null     $protocol    Protocol.
     * @return  void 
     */
    public function __construct($content= '', iterable $headers = array(), ?string $protocol = null)
    {
        $this->content = $content;
        $this->headers = new Arr($headers);
        $this->protocol = $protocol;
    }

    /**
     * Return the headers or get an individual header.
     * 
     * @param   string|null      $key       Key of header to get.
     * @param   mixed            $default   If header not there.
     * @return  mixed
     */
    public function header(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->headers;
        } else if ($this->headers->has($key)) {
            return $this->headers->get($key);
        }
        return $default;
    }

    /**
     * Set the body.
     *
     * @param   mixed       $content       Body content.
     * @return  ResponseInterface
     */
    public function setContent($content) : MessageInterface
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the content.
     * 
     * @return  mixed 
     */
    public function getContent()
    {
        return $this->content;
    }

    // =============================================
    // PSR
    // =============================================

    /**
     * See if we have a header.
     * 
     * @param   string  $name   Header name.
     * @return  bool
     */
    public function hasHeader(string $name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * Get a header.
     * 
     * @param   string  $name       Name of header to get.
     * @return  array
     */
    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            return $this->headers->get($name)->toArray();
        }
        return [];
    }

    /**
     * Get all the headers.
     * 
     * @return  array
     */
    public function getHeaders(): array
    {
        return $this->headers->toArray();
    }

    /**
     * Get a header line.
     * 
     * @param   string  $name       Name of header to get.
     * @return  string
     */
    public function getHeaderLine(string $name): string
    {
        if ($this->hasHeader($name)) {
            return implode(',', $this->headers->get($name)->toArray());
        }
        return '';
    }

    /**
     * Set a header.
     * 
     * @param   string      $name       Name of header to set.
     * @param   string[]    $values     Array of values for this header.
     * @return  MessageInterface 
     */
    public function setHeader(string $name, array $values): MessageInterface
    {
        $this->headers->set($name, $values);
        return $this;
    }

    /**
     * Remove a header.
     * 
     * @param   string      $name       Name of header to set.
     * @return  MessageInterface 
     */
    public function removeHeader(string $name): MessageInterface
    {
        if ($this->hasHeader($name)) {
            $this->headers->offsetUnset($name);
        }
        return $this;
    }

    /**
     * Add a header line.
     * 
     * @param   string      $name       Name of header to set.
     * @param   string      $value      Value of header line.
     * @return  MessageInterface 
     */
    public function addHeaderLine(string $name, string $value): MessageInterface
    {
        if (!$this->hasHeader($name)) {
            $this->setHeader($name, array());
        }
        $tmp = $this->getHeader($name);
        $tmp[] = $value;
        return $this->setHeader($name, $tmp);
    }

    /**
     * Create a version of ourself with a new header.
     * 
     * @param   string              $name       Name of header to replace.
     * @param   array|string        $values     Values for the header.
     * @return  MessageInterface
     */
    public function withHeader(string $name, $values): MessageInterface
    {
        $clone = clone($this);
        if (!is_array($values)) {
            $values = [$values];
        }
        $clone->setHeader($name, $values);
        return $clone;
    }

    /**
     * Create a version of ourself with a new header line added to a header.
     * 
     * @param   string              $name       Name of header to replace.
     * @param   array|string        $values     Values for the header.
     * @return  MessageInterface
     */
    public function withAddedHeader(string $name, $values): MessageInterface
    {
        $clone = clone($this);
        $tmp = [];
        if ($this->hasHeader($name)) {
            $tmp = $this->getHeader($name);
        }
        if (!is_array($values)) {
            $values = [$values];
        }
        foreach ($values as $val) {
            $tmp[] = $val;
        }
        $clone->setHeader($name, $tmp);
        return $clone;
    }

    /**
     * Create a version of ourself without a specific header.
     * 
     * @param   string              $name       Name of header to remove.
     * @return  MessageInterface
     */
    public function withoutHeader(string $name): MessageInterface
    {
        $clone = clone($this);
        $clone->removeHeader($name);
        return $clone;
    }


    /**
     * Split a protocol.
     * 
     * @param   string          $protocol   Protocol to split.
     * @param   string          $by         What to split by.
     * @return  array
     */
    protected function splitProtocol(string $protocol, string $by = '/'): array
    {
        return explode($by, $protocol);
    }

    /**
     * Set the protocol.
     * 
     * @param   string           $protocol   Protocol.
     * @return  MessageInterface
     */
    public function setProtocol(string $protocol): MessageInterface
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Get the protocol version.
     * 
     * @return  string
     */
    public function getProtocolVersion(): string
    {
        $ret = null;
        if (!is_null($this->protocol)) {
            list($start, $version) = $this->splitProtocol($this->protocol);
            $ret = $version;
        }
        return $ret;
    }

    /**
     * Create a clone with a new protocol version.
     * 
     * @param   string  $newVersion    New version.
     * @return  MessageInterface
     */
    public function withProtocolVersion(string $newVersion): MessageInterface
    {
        list($start, $version) = $this->splitProtocol($this->protocol);
        $clone = clone($this);
        $this->setProtocol($start . '/' . $newVersion);
        return $clone;
    }
}
