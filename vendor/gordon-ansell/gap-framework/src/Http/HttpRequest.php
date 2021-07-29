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

use GreenFedora\Message\Request;
use GreenFedora\Http\HttpRequestInterface;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Uri\Uri;
use GreenFedora\Uri\UriInterface;

use GreenFedora\Http\Exception\InvalidArgumentException;

/**
 * HTTP request class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class HttpRequest extends Request implements HttpRequestInterface
{
    /**
     * Variable sets to load.
     * @var array
     */
    protected $varSets = array('get', 'post', 'request', 'env', 'server', 'cookie', 'session', 'files', 'header');
     
    /**
     * Request URI.
     * @var Uri|null
     */
    protected $requestUri   =  null;

    /**
     * Variables.
     * @var ArrInterface|null
     */
    protected $get         =   null;
    protected $post        =   null;
    protected $request     =   null;
    protected $server      =   null;
    protected $cookie      =   null;
    protected $files       =   null;

    //protected $session     =   null;
    //protected $env         =   null;

    /**
     * Is dispatched?
     * @var bool
     */
    protected $isDispatched    =   false;
    
    /**
     * Constructor.
     *
     * @param   iterable                $get            GET.
     * @param   iterable                $post           POST.
     * @param   iterable                $cookies        COOKIES.
     * @param   iterable                $files          FILES.
     * @param   iterable                $server         SERVER.
     * @param   iterable                $headers        Headers
     * @param   mixed                   $content        Request content.
     * @param   string|null             $protocol       Protocol.
     * @return  void
     */
    public function __construct(iterable $get = array(), iterable $post = array(),
        iterable $cookies = array(), iterable $files = array(), iterable $server = array(), 
        iterable $headers = array(), $content = null, ?string $protocol = null)
    {
        parent::__construct($content, $headers, $protocol);

        $this->get = new Arr($get);
        $this->post = new Arr($post);
        $this->cookies = new Arr($cookies);
        $this->files = new Arr($files);
        $this->server = new Arr($server);
    }

    /**
     * Create one of these from the environment.
     * 
     * @return MessageInterface
     */
    public static function fromGlobals(): HttpRequestInterface
    {
        $protocol = 'HTTP/1.1';
        if (array_key_exists('SERVER_PROTOCOL', $_SERVER)) {
            $protocol = $_SERVER['SERVER_PROTOCOL'];
        }

        $request = new static( 
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER,
            self::extractHeaders($_SERVER),
            null,
            $protocol
        );

        if (0 === strpos($request->headers->get('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded')
            and in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->constructContent(), $data);
            $request->request = new Arr($data);
        }

        return $request;
    }

    /**
     * Extract the headers.
     * 
     * @param   iterable    $base   Base to extract from.
     * @return  array
     */
    public static function extractHeaders(iterable $base): array
    {
        $headers = [];
        foreach ($base as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headers[$key] = $value;
            }
        }

        if (isset($base['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $base['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = $base['PHP_AUTH_PW'] ?? '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} .+
             * RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} .+
             * RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authorizationHeader = null;
            if (isset($base['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $base['HTTP_AUTHORIZATION'];
            } elseif (isset($base['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $base['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (2 == count($exploded)) {
                        [$headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']] = $exploded;
                    }
                } elseif (empty($base['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $base['PHP_AUTH_DIGEST'] = $authorizationHeader;
                } elseif (0 === stripos($authorizationHeader, 'bearer ')) {
                    /*
                     * XXX: Since there is no PHP_AUTH_BEARER in PHP predefined variables,
                     *      I'll just set $headers['AUTHORIZATION'] here.
                     *      https://php.net/reserved.variables.server
                     */
                    $headers['AUTHORIZATION'] = $authorizationHeader;
                }
            }
        }

        if (isset($headers['AUTHORIZATION'])) {
            return $headers;
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;

    }

    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @return string|resource The request body content or a resource to read the body stream
     */
    public function constructContent(bool $asResource = false)
    {
        $currentContentIsResource = is_resource($this->content);

        if (true === $asResource) {
            if ($currentContentIsResource) {
                rewind($this->content);
                return $this->content;
            }

            // Content passed in parameter (test)
            if (is_string($this->content)) {
                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $this->content);
                rewind($resource);
                return $resource;
            }

            $this->content = false;
            return fopen('php://input', 'r');
        }

        if ($currentContentIsResource) {
            rewind($this->content);
            return stream_get_contents($this->content);
        }

        if (null === $this->content or false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    	
    /**
     * Get the request URI.
     *
     * @return  UriInterface
     */
    public function getRequestUri() : UriInterface
    {
        if (null === $this->requestUri) {
            $scheme = (isset($_SERVER['HTTPS']) and !empty($_SERVER['HTTPS']) and ('on' == $_SERVER['HTTPS']))
                ? 'https://' : 'http://';
            $this->requestUri = new Uri($scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }
        return $this->requestUri;
    }

    /**
     * Get the route info.
     *
     * @return  string|null
     */
    public function getRoute() : ?string
    {
        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    }

    /**
     * Get the base URI.
     *
     * @return  string
     */
    public function getBaseUri() : string
    {
	    $ret = $this->getRequestUri()->getUri();
	    if ('/' != $this->getRoute()) {
        	$ret = str_replace($this->getRoute(), '', $ret);
        }
        return rtrim($ret, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Get the request method.
     *
     * @return  string
     */
    public function getMethod() : string
    {
        return $this->server->get('REQUEST_METHOD');
    }

    /**
     * See if this is a POST request.
     *
     * @return  bool
     */
    public function isPost() : bool
    {
        return 'POST' == $this->getMethod();
    }

    /**
     * Set the request as dispatched.
     *
     * @param   bool            $flag       Flag to set.
     * @return  RequestInterface
     */
    public function setDispatched(bool $flag = true) : HttpRequestInterface
    {
        $this->isDispatched = $flag;
        return $this;
    }

    /**
     * Have we been dispatched?
     *
     * @return  bool
     */
    public function isDispatched() : bool
    {
        return $this->isDispatched;
    }

    /**
     * Get get variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function get(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->get;
        } else if ($this->get->has($key)) {
            return $this->get->get($key);
        }
        return $default;
    }

    /**
     * Get post variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function post(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->post;
        } else if ($this->post->has($key)) {
            return $this->post->get($key);
        }
        return $default;
    }

    /**
     * See if we have a post variable.
     *
     * @param   string          $key        Key to get.
     * @return  bool
     */
    public function hasPost(string $key) : bool
    {
        return $this->post->has($key);
    }

    /**
     * Check the form submitted.
     *
     * @param   string  $form   Form name to check.
     * @return  bool
     */
    public function formSubmitted(string $form): bool
    {
        return ($this->isPost() and $this->post('form-submitted') == $form);
    }

    /**
     * Get request variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function request(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request;
        } else if ($this->request->has($key)) {
            return $this->request->get($key);
        }
        return $default;
    }

    /**
     * Get server variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function server(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->server;
        } else if ($this->server->has($key)) {
            return $this->server->get($key);
        }
        return $default;
    }

    /**
     * Get cookie variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function cookie(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->cookie;
        } else if ($this->cookie->has($key)) {
            return $this->cookie->get($key);
        }
        return $default;
    }

    /**
     * Get files variable(s).
     *
     * @param   string          $key        Key to get.
     * @param   mixed           $default    Default if not found.
     * @return  mixed
     */
    public function files(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->files;
        } else if ($this->files->has($key)) {
            return $this->files->get($key);
        }
        return $default;
    }

}

