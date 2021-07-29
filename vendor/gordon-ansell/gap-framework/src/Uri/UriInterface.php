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

/**
 * Interface for the Uri class.
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
interface UriInterface
{
	/**
	 * Set the base URI.
	 *
	 * @param 	string		$baseUri 	Base URI to set.
	 * @return 	UriInterface
	 */
	public function setBaseUri(string $baseUri) : UriInterface;
	
	/**
	 * Get the base URI.
	 *
	 * @return 	string
	 */
	public function getBaseUri() : string;

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
    public function getParts(): array;

    /**
     * Get the URI.
     *
     * @param 	bool|null 	$returnSchemeForFiles		Return the scheme for files?
     * @return  string
     */
    public function getUri(?bool $returnSchemeForFiles = null) : string;

    /**
     * Return string, which is just the unparsed URI.
     *
     * @return  string
     */
    public function __toString() : string;
    
    /**
	 * Get the absolute URI.
	 *
	 * @param 	string|null 	$baseUri	Base URI to use.
	 * @return 	string
     * @throws  InvalidArgumentException
	 */
	public function getAbsolute(?string $baseUri = null) : string;

    /**
	 * Get the relative URI.
	 *
	 * @param 	string|null 	$baseUri	Base URI to use.
	 * @return 	string
     * @throws  InvalidArgumentException
	 */
	public function getRelative(?string $baseUri = null) : string;

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
    public function getScheme() : string;

    /**
     * Set the scheme.
     *
     * @param   string      $scheme         Scheme to set.
     * @return  UriInterface
     */
    public function setScheme(string $scheme) : UriInterface;

    /**
     * =================================================================================
     * Authority.
     * =================================================================================
     */

    /**
     * Get the authority.
     * @return  string
     */
    public function getAuthority() : string;

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
    public function getUser() : string;

    /**
     * Set the user.
     *
     * @param   string          $user           User to set.
     * @return  UriInterface
     */
    public function setUser(string $user) : UriInterface;

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
    public function getPassword() : string;

    /**
     * Set the password.
     *
     * @param   string          $pass           Password to set.
     * @return  UriInterface
     */
    public function setPassword(string $pass) : UriInterface;

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
    public function getHost() : string;

    /**
     * Set the host.
     *
     * @param   string      $host         Host to set.
     * @return  UriInterface
     */
    public function setHost(string $host) : UriInterface;

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
    public function getPort(bool $ignoreStandard = true) : string;

    /**
     * Set the port.
     *
     * @param   int      $port         Port to set.
     * @return  UriInterface
     */
    public function setPort(int $port) : UriInterface;

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
    public function getBasePath() : string;

    /**
     * Get everything excluding the base path.
     *
     * @return string.
     */
    public function getExcludingBasePath() : string;

    /**
     * Get the path.
     *
     * @return  string
     */
    public function getPath() : string;

    /**
     * Set the path.
     *
     * @param   string      $path         Path to set.
     * @return  UriInterface
     */
    public function setPath(string $path) : UriInterface;

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
    public function getDomain(): string;

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
    public function getQuery() : string;

    /**
     * Get the query params as an array.
     *
     * @return  array
     */
    public function getQueryArray() : array;

    /**
     * Set the query.
     *
     * @param   string|array      $query         Query to set.
     *
     * @return  UriInterface
     * @throws  InvalidArgumentException		 If the passed parameter is neither an array nor a string.
     */
    public function setQuery($query) : UriInterface;

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
    public function getFragment() : string;

    /**
     * Set the fragment.
     *
     * @param   string      $fragment         Fragment to set.
     * @return  UriInterface
     */
    public function setFragment(string $fragment) : UriInterface;

}
