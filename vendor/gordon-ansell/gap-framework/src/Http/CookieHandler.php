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

use GreenFedora\Http\HttpRequestInterface;
use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * HTTP cookie handler class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class CookieHandler
{
    /**
     * Request.
     * @var RequestInterface
     */
    protected $request = null;

    /**
     * Cookie names and defaults.
     * @var array
     */
    protected $names = [];

    /**
     * Cookie prefix.
     * @var string
     */
    protected $prefix = null;

    /**
     * Constructor.
     * 
     * @param   HttpRequestInterface    $request    Request.
     * @param   array                   $names      Cookie names and defaults.
     * @param   string                  $prefix     Cookie prefix.
     * @return  void
     */
    public function __construct(HttpRequestInterface $request, array $names, string $prefix)
    {
        $this->request = $request;
        $this->names = $names;
        $this->prefix = $prefix;
    }

    /**
     * Load the cookies.
     * 
     * @param   ArrInterface    $target     Where to load them.
     * @return  void
     */
    public function load(ArrInterface &$target)
    {
        foreach ($this->names as $key => $default) {
            $target->set($key, $this->request->cookie($this->prefix . $key, $default));
        }
    }

    /**
     * Save the cookies.
     * 
     * @param   ArrInterface    $source     Where to get the data.
     * @return  void
     */
    public function save(ArrInterface $source)
    {
        foreach ($this->names as $key => $default) {
            if ($source->has($key)) {
                setcookie($this->prefix . $key, strval($source->get($key)));
            } else {
                setcookie($this->prefix . $key, strval($default));
            }
        }
    }
}

