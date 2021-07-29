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

use GreenFedora\Message\Response;
use GreenFedora\Http\HttpResponseInterface;

/**
 * HTTP response class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class HttpResponse extends Response implements HttpResponseInterface
{

    /**
     * Constructor.
     *
     * @param   mixed           $content            Content.
     * @param   array           $headers            Headers.
     * @param   int             $statusCode         Status code.
     * @param   string|null     $protocol           Protocol.
     * @param   bool            $renderExceptions   Should we?
     * @return  void
     */
    public function __construct($content = '', array $headers = array(), int $statusCode = 200, 
        ?string $protocol = null, bool $renderExceptions = true)
    {
        parent::__construct($content, $headers, $protocol, $renderExceptions);
        $this->statusCode = $statusCode;
    }

    /**
     * Create one of these from the environment.
     * 
     * @param   mixed            $content            Content.
     * @param   array           $headers            Headers.    
     * @param   bool            $renderExcaptions   Should we?
     * @return MessageInterface
     */
    public static function fromEnvironment($content = '', array $headers = array(), 
        bool $renderExceptions = true): HttpResponseInterface
    {
        $protocol = 'HTTP/1.1';
        if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
            $protocol = $_SERVER['SERVER_PROTOCOL'];
        }

        return new static($content, $headers, 200, $protocol, $renderExceptions);
    }


}

