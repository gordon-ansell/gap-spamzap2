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

/**
 * Interface for the Arr class.
 */
interface ArrInterface
{
    /**
     * Load some values.
     *
     * @param   iterable|\Traversable       $vals           Values to load.
     * @return  ArrInterface
     * @throws  InvalidArgumentException
     */
    public function loadValues($vals): ArrInterface;

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
	public function offsetSet($index, $newVal): void;

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
	public function sortByCol(string $col, int $spec = SORT_ASC): void;

    /**
	 * Sort array by keys.
	 *
	 * @param 	int 	$flags 		Sort spec.	
	 * @return 	void
	 */
	public function ksort($flags = SORT_REGULAR): void;

    /**
	 * Sort array by values (sequential).
	 *
	 * @param 	int 	$flags 		Sort spec.	
	 * @return 	void
	 */
	public function sort($flags = SORT_REGULAR): void;

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
    public function notBeginningWith($begin, bool $preserve = true, bool $numKeys = false): ArrInterface;

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
	public function in($test, bool $strict = false): bool;
	
    /**
     * See if something's NOT in this array.
     *
     * @param 	mixed 	$test		Thing to check.
     * @param 	bool 	$strict 	Strict check?
     * @return  bool
     */
    public function notIn($test, bool $strict = false): bool;

    /**
	 * Get the keys. Emulates array_keys().
	 * @return 	array
	 */
	public function keys(): array;

	/**
	 * Do a recursive merge-replace.
	 *
	 * @param	interable	    $new				New data to merge in with us.
	 * @return	ArrInterface
	 * @throws	InvalidArgumentException	        If something we passed in is a bit naff.
	 * @throws 	RuntimeException
	 */
	public function mergeReplaceRecursive(iterable $new) : ArrInterface;

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
	public function key(int $index);
	
	/**
	 * Get an element at a given index.
	 *
	 * @param 	int 	$index 		Index to get element from.
	 * @return 	mixed
	 */
	public function at(int $index);
	
	/**
	 * Simply return true if we have an element and it's exactly true.
	 *
	 * @param 	mixed 		$key 	Element key.
	 * @return 	bool
	 */
	public function isTrue($key): bool;

    /**
     * See if an element is (strictly) true.
     *
     * @param 	string 	$key 		Key to check.
     * @return  bool
     */
    public function isStrictlyTrue(string $key): bool;

    /**
	 * Is this a sequential array?
     * 
	 * @return 	bool
	 */
	public function isSequential(): bool;

    /**
     * Get the key of something in this array.
     *
     * @param 	mixed 	$thing 		Thing to check.
     * @param 	bool 	$strict 	Strict check?
     * @return  string|null
     */
    public function indexOf($thing, bool $strict = false) : ?string;

    /**
     * Get the (string) key for something at a given index.
     *
     * @param 	int 	$index 		Index to look up.
     * @return  string
     */
    public function keyFromIndex(int $index): string;
    
    /**
     * Get a value by its index (or default).
     *
     * @param   int      	$index          Index.
     * @param   mixed       $default        Default.
     * @return  mixed
     */
    public function getByIndex(int $index, $default = null);
    
    /**
     * Count elements.
     *
     * @param 	string 	$key 		Key to check.
     * @return  int
     */
    public function cnt(string $key): int;

    // =============================================================
    // GETTERS AND SETTERS.
    // =============================================================

	/**
	 * See if we have a particular value.
	 *
	 * @param 	mixed 		$key				Key to check.
	 * @return 	bool
	 */
	public function has($key): bool;
	
	/**
	 * Get a value or return a default.
	 *
	 * @param	mixed 		$key				Key of value to get.
	 * @param 	mixed 		$default 			Default if key does not exist.
	 * @param	bool	    $except				Trigger exception if not found?
	 * @return	mixed
     * @throws  OutOfBoundsException
	 */
	public function get($key, $default = null, bool $except = false);
	
	/**
	 * Set a value,
	 *
	 * @param 	mixed 		$key 				Key of value to set.
	 * @param 	mixed 		$val 				Value to set.
	 * @return 	ArrInterface
	 */
	public function set($key, $val): ArrInterface;
	
	/**
	 * Unset a value,
	 *
	 * @param 	mixed 		$key 				Key of value to unset.
	 * @return 	ArrInterface
	 */
	public function unset($key): ArrInterface;

    /**
	 * Permit object access.
	 *
	 * @param	mixed 		$key 				Key of value to get.
	 * @param 	mixed 		$val 				Value to set.
	 * @return	void
	 */
	public function __set($key, $val): void;

	/**
	 * Permit object access.
	 *
	 * @param	mixed 		$key 				Key of value to get.
	 * @return	mixed
	 */
	public function __get($key);

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
	public function toArray(bool $preserveObjects = false, bool $numKeys = false, bool $preserveSelf = false): array;

    // =============================================================
    // DEBUGGING.
    // =============================================================

	/**
	 * Dump the object.
	 * 
	 * @return 	array 
	 */
	public function dump(): array;
}
