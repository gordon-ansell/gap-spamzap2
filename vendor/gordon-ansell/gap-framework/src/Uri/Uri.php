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
namespace GreenFedora\Uri;

use GreenFedora\Uri\UriInterface;
use GreenFedora\Uri\Exception\InvalidArgumentException;

/**
 * Manipulates URIs (Uniform Resource Identifiers).
 * 
 * @link https://en.wikipedia.org/wiki/Uniform_Resource_Identifier
 * 
 * @property        string      $scheme     Scheme.
 * @property        string      $host       Host.
 * @property        int         $port       Port.
 * @property        string      $user       User.
 * @property        string      $pass       Password.
 * @property        string      $path       Path.
 * @property        string      $query      Query.
 * @property        string      $fragment   Fragment.
 * 
 * @property-read   string      $authority  User + pass + host + port.
 */
class Uri implements UriInterface
{
	/**
	 * Web schemes.
	 * @var array
	 */
	const WEBSCHEMES = array('http', 'https', 'telnet', 'ftp', 'ldap');

    /**
     * Valid parts of the URI.
     */
    const PARTS = array('scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment');
	 	
	/**
	 * Raw URI passed in.
	 * @var string|null
	 */
	protected $rawUri = null;
	
	/**
	 * Parts of the URI.
	 * @var array
	 */
	protected $parts = array();	
	
	/**
	 * Return scheme for file paths?
	 * @var bool
	 */
	protected $returnSchemeForFiles = false;
	
	/**
	 * Base URI.
	 * @var string
	 */
	protected $baseUri = null;	
	
	/**
	 * Static base URI.
	 * @var string
	 */
	protected static $baseUriStatic = null;	
	
	/**
	 * Constructor.
	 *
	 * @param	string		$uri 					Input URI.
	 * @param 	bool 		$isRelative 			Hard-code this as a relative Uri?
	 * @param 	string|null	$baseUri				Base URI.
	 * @param 	string 		$defaultScheme			Default scheme to use if one isn't specified.
	 * @param 	bool 		$returnSchemeForFiles	Do you want the scheme added to files on return?
	 * @return 	void
	 */
	public function __construct(string $uri, bool $isRelative = false, ?string $baseUri = null, 
    string $defaultScheme = 'file', bool $returnSchemeForFiles = false)
	{
		$this->rawUri = self::slashes($uri);
		if ($isRelative) {
			$this->rawUri = ltrim($this->rawUri, DIRECTORY_SEPARATOR);
		}
		$this->baseUri = (is_null($baseUri)) ? self::$baseUriStatic : rtrim(self::slashes($baseUri), DIRECTORY_SEPARATOR);
		$this->returnSchemeForFiles = $returnSchemeForFiles;
		$this->parse($defaultScheme);
	}	
	
	/**
	 * Parse the URI.
	 *
	 * @param 	string 		$defaultScheme		Default scheme to use if one isn't specified.
	 * @return 	void
	 */
	protected function parse(string $defaultScheme): void
	{
		$this->parts = parse_url($this->rawUri);	
		if (!array_key_exists('scheme', $this->parts)) {
			$this->parts['scheme'] = $defaultScheme;
		}	
	}
		
	/**
	 * Set the base URI.
	 *
	 * @param 	string		$baseUri 	Base URI to set.
	 * @return 	UriInterface
	 */
	public function setBaseUri(string $baseUri) : UriInterface
	{
		$this->baseUri = rtrim(self::slashes($baseUri), DIRECTORY_SEPARATOR);
		return $this;
	}	
	
	/**
	 * Get the base URI.
	 *
	 * @return 	string
	 */
	public function getBaseUri() : string
	{
		return $this->baseUri;
	}	

    /**
     * =================================================================================
     * Access to the whole thing.
     * =================================================================================
     */

    /**
     * Get the URI array parts.
     *
     * @return array
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Magic getter to get parts of the URI.
     * 
     * @param   string  $part   Part to get.
     * @return  mixed
     * @throws  InvalidArgumentException 
     */
    public function __get(string $part)
    {
        if ('authority' === $part) {
            return $this->getAuthority();
        }
        if (in_array($part, self::PARTS)) {
            if (array_key_exists($part, $this->parts)) {
                return $this->parts[$part];
            } else {
                return '';
            }
        }
        throw new InvalidArgumentException(sprintf("Invalid URI part: %s.", $part));
    }

    /**
     * Magic setter to set parts of the URI.
     * 
     * @param   string  $part   Part to set.
     * @param   mixed   $val    Value to set.
     * @return  mixed
     * @throws  InvalidArgumentException 
     */
    public function __set(string $part, $val)
    {
        if (in_array($part, self::PARTS)) {
            $this->parts[$part]= $val;
        }
        throw new InvalidArgumentException(sprintf("Invalid URI part: %s.", $part));
    }

    /**
     * Get the URI.
     *
     * @param 	bool|null 	$returnSchemeForFiles		Return the scheme for files?
     * @return  string
     */
    public function getUri(?bool $returnSchemeForFiles = null) : string
    {
	    $returnSchemeForFiles = (null === $returnSchemeForFiles) ? $this->returnSchemeForFiles : $returnSchemeForFiles;
        return self::unparse($this->parts, $returnSchemeForFiles);
    }

    /**
     * Return string, which is just the unparsed URI.
     *
     * @return  string
     */
    public function __toString() : string
    {
        return $this->getUri();
    }
    
    /**
	 * Get the absolute URI.
	 *
	 * @param 	string|null 	$baseUri	Base URI to use.
	 * @return 	string
     * @throws  InvalidArgumentException
	 */
	public function getAbsolute(?string $baseUri = null) : string
	{
		$uri = $this->getUri();
		
		$baseUri = is_null($baseUri) ? $this->baseUri : self::slashes($baseUri);
		
		if (self::isAbsolute($uri)) {
			return $uri;
		}
		
		if (!is_null($baseUri)) {
			return rtrim($baseUri, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($uri, DIRECTORY_SEPARATOR);
		}
		
		throw new InvalidArgumentException(sprintf("Cannot return absolute URI for '%s' because base URI is not set", $uri));
	}   

    /**
	 * Get the relative URI.
	 *
	 * @param 	string|null 	$baseUri	Base URI to use.
	 * @return 	string
     * @throws  InvalidArgumentException
	 */
	public function getRelative(?string $baseUri = null) : string
	{
		$uri = $this->getUri();
		
		$baseUri = is_null($baseUri) ? $this->baseUri : self::slashes($baseUri);
		
		if (!self::isAbsolute($uri)) {
			return $uri;
		}
		
		if (!is_null($baseUri)) {
			return str_replace($baseUri, '', $uri);
		}
		
		throw new InvalidArgumentException(sprintf("Cannot return relative URI for '%s' because base URI is not set", $uri));
	}   

    /**
     * =================================================================================
     * Scheme.
     * =================================================================================
     */

    /**
     * Get the scheme.
     *
     * @return  string
     */
    public function getScheme() : string
    {
        return $this->parts['scheme'] ?? '';
    }

    /**
     * Set the scheme.
     *
     * @param   string      $scheme         Scheme to set.
     * @return  UriInterface
     */
    public function setScheme(string $scheme) : UriInterface
    {
        $this->parts['scheme'] = $scheme;
        return $this;
    }

    /**
     * =================================================================================
     * Authority.
     * =================================================================================
     */

    /**
     * Get the authority.
     * 
     * @return  string
     */
    public function getAuthority() : string
    {
        $ret = '';
        $ret .= self::constructAuth($this->parts);
        $ret .= $this->parts['host'] ?? '';
        $ret .= isset($this->parts['port']) ? ':' . $this->parts['port'] : '';
        return $ret;
    }

    /**
     * =================================================================================
     * User.
     * =================================================================================
     */

    /**
     * Get the user.
     * 
     * @return  string
     */
    public function getUser() : string
    {
        return $this->parts['user'] ?? '';
    }

    /**
     * Set the user.
     *
     * @param   string          $user           User to set.
     * @return  UriInterface
     */
    public function setUser(string $user) : UriInterface
    {
        $this->parts['user'] = $user;
        return $this;
    }

    /**
     * =================================================================================
     * Password.
     * =================================================================================
     */

    /**
     * Get the password.
     *
     * @return  string
     */
    public function getPassword() : string
    {
        return $this->parts['pass'] ?? '';
    }

    /**
     * Set the password.
     *
     * @param   string          $pass           Password to set.
     * @return  UriInterface
     */
    public function setPassword(string $pass) : UriInterface
    {
        $this->parts['pass'] = $pass;
        return $this;
    }

    /**
     * =================================================================================
     * Host.
     * =================================================================================
     */

    /**
     * Get the host.
     *
     * @return  string
     */
    public function getHost() : string
    {
        return $this->parts['host'] ?? '';
    }

    /**
     * Set the host.
     *
     * @param   string      $host         Host to set.
     * @return  UriInterface
     */
    public function setHost(string $host) : UriInterface
    {
        $this->parts['host'] = $host;
        return $this;
    }

    /**
     * =================================================================================
     * Port.
     * =================================================================================
     */

    /**
     * Get the port.
     *
     * @param   bool        $ignoreStandard     Don't return standard ports.
     * @return  string
     */
    public function getPort(bool $ignoreStandard = true) : string
    {
        if (static::isStandardPort($this->parts) and $ignoreStandard) {
            return '';
        }
        return (string) ($this->parts['port']) ?? '';
    }

    /**
     * Set the port.
     *
     * @param   int      $port         Port to set.
     * @return  UriInterface
     */
    public function setPort(int $port) : UriInterface
    {
        $this->parts['port'] = $port;
        return $this;
    }

    /**
     * =================================================================================
     * Path.
     * =================================================================================
     */

    /**
     * Get the base path.
     *
     * @return string
     */
    public function getBasePath() : string
    {
        $parts = $this->parts;
        $parts['scheme'] = null;
        $parts['user'] = null;
        $parts['pass'] = null;
        $parts['host'] = null;
        $parts['port'] = null;
        return self::unparse($parts);
    }

    /**
     * Get everything excluding the base path.
     *
     * @return string.
     */
    public function getExcludingBasePath() : string
    {
        $parts = $this->parts;
        $parts['path'] = null;
        $parts['query'] = null;
        $parts['fragment'] = null;
        return self::unparse($parts);
    }

    /**
     * Get the path.
     *
     * @return  string
     */
    public function getPath() : string
    {
        return $this->parts['path'] ?? '';
    }

    /**
     * Set the path.
     *
     * @param   string      $path         Path to set.
     * @return  UriInterface
     */
    public function setPath(string $path) : UriInterface
    {
        $this->parts['path'] = $path;
        return $this;
    }

    /**
     * =================================================================================
     * Domain.
     * =================================================================================
     */

    /**
     * Get the domain.
     *
     * @return  string
     */
    public function getDomain(): string
    {
        if (!array_key_exists('host', $this->parts)) {
            return '';
        }

        $work = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->parts['host']);
        $work = trim($work, DIRECTORY_SEPARATOR);
        $sp = explode(DIRECTORY_SEPARATOR, $work);
        return $sp[0];
    }

    /**
     * =================================================================================
     * Query.
     * =================================================================================
     */

    /**
     * Get the query.
     *
     * @return  string
     */
    public function getQuery() : string
    {
        return $this->parts['query'] ?? '';
    }

    /**
     * Get the query params as an array.
     *
     * @return  array
     */
    public function getQueryArray() : array
    {
        parse_str($this->parts['query'], $ret);
        return $ret;
    }

    /**
     * Set the query.
     *
     * @param   string|array      $query         Query to set.
     *
     * @return  UriInterface
     * @throws  InvalidArgumentException		 If the passed parameter is neither an array nor a string.
     */
    public function setQuery($query) : UriInterface
    {
        if (is_array($query)) {
            $this->parts['query'] = http_build_query($query);
        } else if (is_string($query)) {
            $this->parts['query'] = $query;
        } else {
            throw new InvalidArgumentException(sprintf("Invalid parameter type '%s' for URI's setQuery", gettype($query)));
        }
        return $this;
    }

    /**
     * =================================================================================
     * Fragment.
     * =================================================================================
     */

    /**
     * Get the fragment.
     *
     * @return  string
     */
    public function getFragment() : string
    {
        return $this->parts['fragment'] ?? '';
    }

    /**
     * Set the fragment.
     *
     * @param   string      $fragment         Fragment to set.
     * @return  UriInterface
     */
    public function setFragment(string $fragment) : UriInterface
    {
        $this->parts['fragment'] = $fragment;
        return $this;
    }

    /**
     * =================================================================================
     * Static helpers.
     * =================================================================================
     */

    /**
     * Is this a standard port?
     *
     * @param   array       $parts      Parts to check.
     * @return  bool
     */
    public static function isStandardPort(array $parts) : bool
    {
	    $scheme = $parts['scheme'];
	    if (self::isWebScheme($scheme) and ('http' == $scheme or 'https' == $scheme)) {
        	$port = $parts['port'] ?? '80';
			return (('http' == $scheme and 80 == $port) or ('https' == $scheme and 443 == $port));
		} else {
			return false;
		}
    }

    /**
     * Construct the authority.
     *
     * @param   array           $parts          Parts of the URI.
     * @return  string
     */
    public static function constructAuth(array $parts) : string
    {
        $upp = '';
        if (isset($parts['user'])) {
            $upp = $parts['user'];
            if (('' != $upp) and (isset($parts['pass']))) {
                $upp .= ':' . $parts['pass'];
            }
            $upp .= '@';
        }
        return $upp;
    }

	/**
	 * Correct the slashes.
	 *
	 * @param	string 		$str 		String to correct them in.
	 * @return 	string
	 */
	public static function slashes(string $str) : string
	{
		return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $str);
	}		

	/**
	 * Is this a web scheme?
	 *
     * @param   string|null     $scheme     Scheme to check.
	 * @return 	bool
	 */
	public static function isWebScheme(?string $scheme) : bool
	{
		return (!is_null($scheme) and !empty($scheme) and in_array($scheme, self::WEBSCHEMES));
	}	
	
	/**
	 * Is this an absolute URI?
	 *
	 * @param 	string		$path		Path to test.
	 * @return 	bool
	 */
	public static function isAbsolute(string $path) : bool
	{
        if (
        	'/' == $path[0] 
        	or 
        	'\\' == $path[0] 
        	or 
        	(strlen($path) > 3 and ctype_alpha($path[0]) and ':' == $path[1] and ('\\' == $path[2] or '/' == $path[2])) 
        	or 
        	!is_null(parse_url($path, PHP_URL_SCHEME))
        ) {
            return true;
        }
        return false;
	}
	
	/**
	 * Set the static base URI.
	 *
	 * @param 	string		$baseUri 	Base URI to set.
	 * @return 	void
	 */
	public static function setBaseUriStatic(string $baseUri)
	{
		self::$baseUriStatic = self::slashes($baseUri);
	}		

    /**
    * Unparse a URI.
    *
    * @param    array           $parts          			Parts of the URI.
    * @param 	bool			$returnSchemeForFiles		Do we want the scheme for files?
    * @return   string                          			URI string.
    */
    public static function unparse(array $parts, bool $returnSchemeForFiles = false) : string
    {
        $port = '';
        $port = self::isStandardPort($parts) ? '' : ('' == $parts['port'] ? '' : ':' . $parts['port']);
        $ret = '';
        if (!is_null($parts['scheme']) and 'file' != $parts['scheme'] and !$returnSchemeForFiles) {
        	$ret .= isset($parts['scheme']) ? $parts['scheme'] . ':' : '';
			$ret .= self::isWebScheme($parts['scheme']) ? '//' : '';
		}
        $ret .= self::constructAuth($parts);
        $ret .= $parts['host'] ?? '';
        $ret .= $port;
        $ret .= $parts['path'] ?? '';
        $ret .= isset($parts['query']) ? '?' . $parts['query'] : '';
        $ret .= isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        return $ret;
    }
}
