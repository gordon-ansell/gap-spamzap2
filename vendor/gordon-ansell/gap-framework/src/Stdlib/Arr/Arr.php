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

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Stdlib\Arr\ArrIter;
use GreenFedora\Stdlib\Arr\Exception\InvalidArgumentException;
use GreenFedora\Stdlib\Arr\Exception\OutOfBoundsException;
use GreenFedora\Stdlib\Arr\Exception\RuntimeException;

/**
 * Array wrapper. Note that this is both iterable and \Traversable
 */
class Arr extends \ArrayObject implements ArrInterface
{
	/**
	 * Flag: preserve types when setting.
	 * @var int
	 */
	const PRESERVE_TYPE_ON_SET =	32;	

    /**
     * Constructor.
     * 
	 * @param	iterable|\Traversable	$input 				Either an array or an object.
	 * @param 	int 		            $flags 				As per \ArrayObject.
	 * @param 	string 		            $iteratorClass		Class to use for iterators.
     * @return  void
     */
    public function __construct($input = [], int $flags = 0, string $iteratorClass = ArrIter::class)
    {
        parent::__construct([], $flags, $iteratorClass);
        $this->loadValues($input);
    }

    /**
     * Load some values.
     *
     * @param   iterable|\Traversable       $vals           Values to load.
     * @return  ArrInterface
     * @throws  InvalidArgumentException
     */
    public function loadValues($vals): ArrInterface
    {
        if ($vals instanceof \Traversable) {
            $vals = static::iteratorToArray($vals);
        }

        if (is_array($vals)) {
            foreach ($vals as $key => $value) {
                if (is_array($value) or ($value instanceof \Traversable)) {
                    parent::offsetSet($key, new static($value));
                } else {
                    parent::offsetSet($key, $value);
                }
            }
        } else if ($vals instanceof ArrInterface) {
            $this->exchangeArray($vals);
        } else {
            throw new InvalidArgumentException(sprintf(
                "Invalid type '%s' supplied to Arr's loadValues. We need something that is iterable or traversable.",
                gettype($vals)
            ));
        }

        return $this;
    }

    // =============================================================
    // BASE CLASS OVERLOADS.
    // =============================================================

	/**
	 * Overload base set to handle creation of new objects.
	 *
	 * @param 	mixed 		$index 				Index to set.
	 * @param 	mixed 		$newVal 			New value to set.
	 * @return 	void
	 */
	public function offsetSet($index, $newVal): void
	{
		$ptos = (($this->getFlags() & self::PRESERVE_TYPE_ON_SET) == self::PRESERVE_TYPE_ON_SET) ? true : false;
		
		if ((is_array($newVal) or $newVal instanceof \Traversable) and !$ptos) {
			parent::offsetSet($index, new static($newVal, $this->getFlags(), $this->getIteratorClass()));
		} else {
			parent::offsetSet($index, $newVal);
		}
	}	

    // =============================================================
    // SORTS.
    // =============================================================

    /**
	 * Sort array by column.
	 *
	 * @param 	string 	$col 		Column to sorty by.
	 * @param 	int 	$spec 		Sort spec.	
	 * @return 	void
	 */
	public function sortByCol(string $col, int $spec = SORT_ASC): void
	{
		$tmp = $this->toArray();
		$ac  = array_column($tmp, $col);
		array_multisort($ac, $spec, $tmp);
		$this->exchangeArray(new static($tmp));
	}

    /**
	 * Sort array by keys.
	 *
	 * @param 	int 	$flags 		Sort spec.	
	 * @return 	void
	 */
	public function ksort($flags = SORT_REGULAR): void
	{
		$tmp = $this->toArray();
		ksort($tmp, $flags);
		$this->exchangeArray(new static($tmp));
	}

    /**
	 * Sort array by values (sequential).
	 *
	 * @param 	int 	$flags 		Sort spec.	
	 * @return 	void
	 */
	public function sort($flags = SORT_REGULAR): void
	{
		$tmp = $this->toArray();
		sort($tmp, $flags);
		$this->exchangeArray(new static($tmp));
	}

    // =============================================================
    // FILTERS AND SEARCHES.
    // =============================================================

    /**
	 * Get all elements that don't have a key that begins with a certain string.   
     *
     * @param 	string|array 	$begin 		String(s) to avoid.
     * @param 	bool			$preserve 	Preserve objects?
     * @param 	bool 			$numKeys	Make numeric keys integers?
     * @return  ArrInterface
     */
    public function notBeginningWith($begin, bool $preserve = true, bool $numKeys = false): ArrInterface
    {
	    $curr = $this->toArray($preserve, $numKeys);
	    
	    if (!is_array($begin)) {
		    $begin = array($begin);
	    }
	    $ret = array();
	    
	    foreach ($curr as $k => $v) {
		    $avoid = false;
		    if (is_string($k)) {
		    	foreach ($begin as $excl) {
					if (substr($k, 0, strlen($excl)) == $excl) {
						$avoid = true;
						break;
					}
		    	}
		    }
		    if ($avoid) {
			    continue;
		    }
		    $ret[$k] = $v;
	    }
	    return new Arr($ret);
	}

    // =============================================================
    // PHP FUNCTION EMULATORS.
    // =============================================================

	/**
	 * See if we contain something. Emulates in_array().
	 *
	 * @param 	mixed 		$test 		Thing to test.
     * @param 	bool 	    $strict 	Strict check?
	 * @return 	bool
	 */
	public function in($test, bool $strict = false): bool	
	{
		return in_array($test, $this->toArray(), $strict);
	}
	
    /**
     * See if something's NOT in this array.
     *
     * @param 	mixed 	$test		Thing to check.
     * @param 	bool 	$strict 	Strict check?
     * @return  bool
     */
    public function notIn($test, bool $strict = false) : bool
    {
	    return !$this->in($test, $strict);
    }

    /**
	 * Get the keys. Emulates array_keys().
	 * @return 	array
	 */
	public function keys(): array
	{
		return array_keys($this->toArray());
	}	

	/**
	 * Do a recursive merge-replace.
	 *
	 * @param	interable	    $new				New data to merge in with us.
	 * @return	ArrInterface
	 * @throws	InvalidArgumentException	        If something we passed in is a bit naff.
	 * @throws 	RuntimeException
	 */
	public function mergeReplaceRecursive(iterable $new) : ArrInterface
	{
		if (!is_array($new)) {
			if (method_exists($new, 'toArray')) {
				$new = $new->toArray();
			} else {
				throw new InvalidArgumentException(sprintf("Argument is of type '%s' and has no 'toArray' method. We need either a raw array or an object with a 'toArray' method.", 
                    gettype($new)));
			}
		}	
		
		$mergeArr = array_replace_recursive($this->toArray(), $new);
		if (is_null($mergeArr)) {
			throw new RuntimeException(sprintf("Null returned from array_replace_recursive. Argument is of of type '%s'.", 
                gettype($new)));
		}
		
		$this->exchangeArray(new static($mergeArr));
		
		return $this;
	}

    // =============================================================
    // ACCESSORS.
    // =============================================================

	/**
	 * Get the array key at a certain index.
	 *
	 * @param 	int 	$index 		Index to get.
	 * @return 	mixed
	 * @throws  OutOfBoundsException
	 */	
	public function key(int $index)
	{
		$keys = $this->keys();
		if (isset($keys[$index])) {
			return $keys[$index];
		}	
		throw new OutOfBoundsException(sprintf("No '%u' key found in Arr instance.", $index));
	}
	
	/**
	 * Get an element at a given index.
	 *
	 * @param 	int 	$index 		Index to get element from.
	 * @return 	mixed
	 */
	public function at(int $index)
	{
		return $this->{$this->key($index)};
	}	
	
	/**
	 * Simply return true if we have an element and it's exactly true.
	 *
	 * @param 	mixed 		$key 	Element key.
	 * @return 	bool
	 */
	public function isTrue($key): bool
	{
		return $this->offsetExists($key) and true === $this->$key;
	}	

    /**
     * See if an element is (strictly) true.
     *
     * @param 	string 	$key 		Key to check.
     * @return  bool
     */
    public function isStrictlyTrue(string $key) : bool
    {
	    return (($this->has($key) and is_bool($this->$key) and true === $this->$key));
    }

    /**
	 * Is this a sequential array?
     * 
	 * @return 	bool
	 */
	public function isSequential(): bool
	{
		return self::isArraySequential($this->toArray());
	}	

    /**
     * Get the key of something in this array.
     *
     * @param 	mixed 	$thing 		Thing to check.
     * @param 	bool 	$strict 	Strict check?
     * @return  string|null
     */
    public function indexOf($thing, bool $strict = false) : ?string
    {
	    $tmp = array_search($thing, $this->toArray(), $strict);
	    return (false === $tmp) ? null : $tmp;
    }

    /**
     * Get the (string) key for something at a given index.
     *
     * @param 	int 	$index 		Index to look up.
     * @return  string
     */
    public function keyFromIndex(int $index): string
    {
	    $keys = array_keys($this->toArray());
	    return $keys[$index];
    }
    
    /**
     * Get a value by its index (or default).
     *
     * @param   int      	$index          Index.
     * @param   mixed       $default        Default.
     * @return  mixed
     */
    public function getByIndex(int $index, $default = null)
    {
	    $key = $this->keyFromIndex($index);
	    
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }
        return $default;
    }
    
    /**
     * Count elements.
     *
     * @param 	string 	$key 		Key to check.
     * @return  int
     */
    public function cnt(string $key) : int
    {
	    if ($this->has($key)) {
	    	return count($this->$key->toArray());
	    }
	    return 0;
    }

    // =============================================================
    // GETTERS AND SETTERS.
    // =============================================================

	/**
	 * See if we have a particular value.
	 *
	 * @param 	mixed 		$key				Key to check.
	 * @return 	bool
	 */
	public function has($key): bool
	{
		return $this->offsetExists($key);
	}	
	
	/**
	 * Get a value or return a default.
	 *
	 * @param	mixed 		$key				Key of value to get.
	 * @param 	mixed 		$default 			Default if key does not exist.
	 * @param	bool	    $except				Trigger exception if not found?
	 * @return	mixed
     * @throws  OutOfBoundsException
	 */
	public function get($key, $default = null, bool $except = false)
	{
		if ($this->has($key)) {
			return $this->offsetGet($key);
		}
				
		if ($except) {
			throw new OutOfBoundsException(sprintf("Arr does not have key '%s'. You can stop this error by passing except = true.", $key));	
		}
		
		return $default;
	}
	
	/**
	 * Set a value,
	 *
	 * @param 	mixed 		$key 				Key of value to set.
	 * @param 	mixed 		$val 				Value to set.
	 * @return 	ArrInterface
	 */
	public function set($key, $val): ArrInterface
	{
		$this->offsetSet($key, $val);
		return $this;
	}		
	
	/**
	 * Unset a value,
	 *
	 * @param 	mixed 		$key 				Key of value to unset.
	 * @return 	ArrInterface
	 */
	public function unset($key): ArrInterface
	{
		$this->offsetUnset($key);
		return $this;
	}		

    /**
	 * Permit object access.
	 *
	 * @param	mixed 		$key 				Key of value to get.
	 * @param 	mixed 		$val 				Value to set.
	 * @return	void
	 */
	public function __set($key, $val): void
	{
		$this->set($key, $val);	
	}		 		

	/**
	 * Permit object access.
	 *
	 * @param	mixed 		$key 				Key of value to get.
	 * @return	mixed
	 */
	public function __get($key)
	{
		return $this->get($key);	
	}	

    // =============================================================
    // CONVERSIONS.
    // =============================================================

    /**
	 * Convert ourselves to a proper array.
	 *
     * @param 	bool	$preserveObjects 		Preserve objects?
     * @param 	bool 	$numKeys		        Make numeric keys integers?
     * @param 	bool	$preserveSelf	        Preserve objects of ourself?
	 * @return 	array
	 */
	public function toArray(bool $preserveObjects = false, bool $numKeys = false, bool $preserveSelf = false): array
	{
		$ret = array();
		foreach ($this as $key => $value) {
	        if ($numKeys and ctype_digit(strval($key))) {
		        $key = intval($key);
	        }
	        
            if (!$preserveSelf and $value instanceof static) {
                $ret[$key] = $value->toArray($preserveObjects, $numKeys, $preserveSelf);
            } else if (!$preserveObjects and is_object($value) and method_exists($value, 'toArray')) {
	            $ret[$key] = $value->toArray($preserveObjects, $numKeys, $preserveSelf);
            } else {
                $ret[$key] = $value;
            }			
		}
		return $ret;
	} 

    // =============================================================
    // DEBUGGING.
    // =============================================================

	/**
	 * Dump layer.
	 * 
	 * @param 	array 	$layer 		Layer.
	 * @param 	array 	$key 		Key.
	 * @param 	array 	$result		Ongoing results.
	 * @return  void
	 */
	protected function dumpLayer(array $layer, array $key = [], array &$result)
	{
		foreach ($layer as $k => $v) {
			if (is_array($v)) {
				array_push($key, $k);
				$key = $this->dumpLayer($v, $key, $result);
			} else if (is_object($v) and method_exists($v, 'toArray')) {
				array_push($key, $k);
				$key = $this->dumpLayer($v->toArray(), $key, $result);
			} else if (is_object($k)) {
				$result[implode('.', $key) . '.' . $k] = get_class($v);
			} else {
				$result[implode('.', $key) . '.' . $k] = $v;
			}
		}
		array_pop($key);
		return $key;
	}

	/**
	 * Dump the object.
	 * 
	 * @return 	array 
	 */
	public function dump(): array
	{
		$ret = [];
		$this->dumpLayer($this->toArray(), [], $ret);
		return $ret;
	}

    // =============================================================
    // STATIC HELPERS.
    // =============================================================

	/**
	 * Determine if an array is sequential.
	 *
	 * @param 	array 	$array		Array to check.
	 * @return 	bool
	 */
	public static function isArraySequential(array $array): bool
	{
		return (array_keys($array) === range(0, count($array) - 1));
	}	

    /**
     * See if an array is associative (i.e. it has at least one non-numeric key).
     * 
     * @param   array       $arr            Array to test.
     * @return  bool
     */
    public static function isArrayAssociative(array $arr): bool
    {
        return (count(array_filter(array_keys($arr), 'is_string')) > 0);
    }

    /**
     * Recursive implode.
     * 
     * @param   string          $sep            Separator.
     * @param   array           $array          Array to implode.
     * @return  string
     */
    public static function implode(string $sep, array $array) : string 
    {
        $ret = '';
        foreach ($array as $item) {
            if ('' != $ret) {
                $ret .= $sep;
            }
            if (is_array($item)) {
                $ret .= self::implode($sep, $item);
            } else {
                $ret .= $item;
            }
        }
        return $ret;
    }

    /**
     * Convert an iterator to an array, recursively.
     *
     * @param   \Traversable    $iterator       To convert.
     * @param   bool            $useKeys        Use element keys as index?
     * @return  array
     */
    public static function iteratorToArray(\Traversable $iterator, bool $useKeys = true): array
    {
        $ret = array();
        foreach ($iterator as $key => $value) {
            if ($value instanceof \Iterator) {
                $value = self::iteratorToArray($value, $useKeys);
            }
            if ($useKeys) {
                $ret[$key] = $value;
            } else {
                $ret[] = $value;
            }
        }
        return $ret;
    }

    /**
     * Create from an array.
     * 
     * @param   array           $input      Input data.
     * @return  ArrInterface
     */
    public static function fromArray(array $input): ArrInterface
    {
        return new static($input);
    }
}
