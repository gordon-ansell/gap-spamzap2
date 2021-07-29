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

use GreenFedora\Message\ResponseInterface;
use GreenFedora\Message\AbstractMessage;
use GreenFedora\Message\Exception\HeadersSentException;

/**
 * Base output.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Response extends AbstractMessage implements ResponseInterface
{	
    /**
     * Status code.
     * @var int
     */
    protected $statusCode   =   200;

    /**
     * Render exceptions?
     * @var bool
     */
    protected $renderExceptions  =   false;

    /**
    * Status code reasons.
    * @var array
    */
    protected $reasons                 =   array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    /**
     * Exceptions.
     * @var \Exception[]
     */
    protected $exceptions   =   array();

    /**
     * Constructor.
     * 
     * @param 	mixed 			$content 	        Content.
     * @param   iterable        $headers            Headers.    
     * @param   string|null     $protocol           Protocol.
     * @param   bool            $renderExceptions   Should we?
     * @return  void 
     */
    public function __construct($content = '', iterable $headers = array(), ?string $protocol = null,
        bool $renderExceptions = true)
    {
		    parent::__construct($content, $headers, $protocol);
            $this->renderExceptions = $renderExceptions;
    }

    /**
     * Set the status code.
     *
     * @param   int         $code       Code to set.
     * @return  ResponseInterface
     */
    public function setStatusCode(int $code) : ResponseInterface
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * See if we can send headers.
     *
     * @param   bool    $throw      Throw exception if sent?
     * @return  bool
     * @throws  HeadersSentException
     */
    public function canSendHeaders(bool $throw = false) : bool
    {
        if (true === headers_sent($file, $line)) {
            if ($throw) {
                throw new HeadersSentException(sprintf("Headers already sent in line %s of file %s", $line, $file));
            }
            return false;
        }
        return true;
    }

    /**
     * Send the headers.
     *
     * @return  ResponseInterface
     */
    public function sendHeaders() : ResponseInterface
    {
        if (count($this->headers) or (200 != $this->statusCode)) {
            $this->canSendHeaders(true);
        } else if (200 == $this->statusCode) {
            return $this;
        }

        $codeSent = false;
        foreach ($this->getHeaders() as $name => $value) {
            if (!$codeSent) {
                if (is_array($value)) {
                    header($name . ': ' . $value[0], true, $this->statusCode);
                } else {
                    header($name . ': ' . $value, false, $this->statusCode);
                }
                $codeSent = true;
            } else {
                if (is_array($value)) {
                    header($name . ': ' . $value[0], true);
                } else {
                    header($name . ': ' . $value, false);
                }
            }
        }

        if (!$codeSent) {
            header($this->protocol . ' ' . $this->statusCode);
        }

        return $this;
    }

    /**
     * Send the content.
     *
     * @return  ResponseInterface
     */
    public function sendContent() : ResponseInterface
    {
        echo $this->content;
        return $this;
    }

    /**
     * Send the request.
     *
     * @return void
     */
    public function send()
    {
        $this->sendHeaders();

        if ($this->hasExceptions() and $this->renderExceptions) {
            foreach ($this->exceptions as $exception) {
                echo self::formatException($exception);
            }
        }

        $this->sendContent();
    }

    /**
    * Format an exception.
    *
    * @param    \Exception  $e      Exception to format.
    * @return   string              Formatted exception.
    */
    public static function formatException(\Exception $e)
    {
        $ret =
            "<b>Exception</b>: " . $e->getMessage() . PHP_EOL . '<br/>' .
            "Thrown on line: " . $e->getLine() . PHP_EOL . '<br/>' .
            "of file: " . $e->getFile(). PHP_EOL . '<br/>' .
            "Stack trace: " . PHP_EOL . '<br/>';
        
        foreach ($e->getTrace() as $k => $v) {
            $ret .= "---" . $k . ": " . PHP_EOL . '<br/>';
            foreach ($v as $k1 => $v1) {
                if (is_array($v1)) {
                    $arr = '';
                    foreach ($v1 as $parm) {
                        if ('' != $arr) {
                            $arr .= ', ';
                        }
                        if (is_object($parm)) {
                            $arr .= get_class($parm);
                        } elseif (is_array($parm)) {
                            $arr .= 'array(' . implode(', ', $parm) . ')';
                        } else {
                            $arr .= $parm;
                        }
                    }
                    $v1 = $arr;
                }
                $ret .= "-------" . $k1 . ": " . $v1 . PHP_EOL . '<br/>';
            }
        }
        
        return $ret;
    }

    /**
     * Add an exception to the response.
     *
     * @param   \Exception    $exception      Exception to add.
     * @return  ResponseInterface
     */
    public function addException(\Exception $exception) : ResponseInterface
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * Do we have exceptions?
     *
     * @return bool
     */
    public function hasExceptions() : bool
    {
        return (count($this->exceptions) > 0);
    }
}
