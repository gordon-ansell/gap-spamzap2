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
namespace GreenFedora\Stdlib\Arr;

use GreenFedora\Stdlib\Arr\DottedArrInterface;
use GreenFedora\Stdlib\Arr\Arr;

/**
 * Dotted access to an array.
 */
class DottedArr extends Arr implements DottedArrInterface
{
    /**
     * See if we have a particular dotted key.
     *
     * @param   string      $key            Key to test.
     * @return  bool
     */
    public function hasDotted(string $key) : bool
    {
        if (false === strpos($key, '.')) {
            return $this->offsetExists($key);
        }

        $split = explode('.', $key);
        if (!$this->offsetExists($split[0])) {
            return false;
        } else {
            $newKey = array_shift($split);
            return $this->offsetGet($newKey)->hasDotted(implode('.', $split));
        }
    }

    /**
     * Get a dotted key.
     *
     * @param   string      $dotted         Dotted key.
     * @param   mixed       $default        Default if not found.
     * @return  mixed
     */
    public function dotted(string $key, $default = null)
    {
        if (false === strpos($key, '.')) {
            return $this->offsetGet($key);
        }

        $split = explode('.', $key);
        if (!$this->offsetExists($split[0])) {
            return $default;
        } else {
            $newKey = array_shift($split);
            return $this->offsetGet($newKey)->dotted(implode('.', $split));
        }
    }

    /**
     * Set a dotted key.
     *
     * @param   string      $key            Dotted key.
     * @param   mixed       $value        	Value to set.
     * @return  void
     */
    public function setDotted(string $key, $value): void
    {
        if (false === strpos($key, '.')) {
            $this->offsetSet($key, $value);
            return;
        } else {
        	$split = explode('.', $key);
		    $newKey = array_shift($split);
		    if (!$this->has($newKey)) {
			    $this->offsetSet($newKey, new static());
		    }
       		$this->offsetGet($newKey)->setDotted(implode('.', $split), $value);	        
        }
    }

    /**
     * Unset a dotted key.
     *
     * @param   string      $key            Dotted key.
     * @return  void
     */
    public function unsetDotted(string $key): void
    {
        if (false === strpos($key, '.')) {
            $this->offsetUnset($key);
            return;
        } else {
        	$split = explode('.', $key);
		    $newKey = array_shift($split);
       		$this->offsetGet($newKey)->unsetDotted(implode('.', $split));	        
        }
    }
}
