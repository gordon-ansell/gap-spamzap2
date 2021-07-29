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

/**
 * Polyfills for PHP 8.0.
 */

if (!function_exists('str_starts_with')) {
    /**
     * String starts with.
     * 
     * @param   string  $haystack   Source string.
     * @param   string  $needle     What we're looking for.
     * @return  bool
     */
    function str_starts_with(string $haystack, string $needle) : bool
    {
        if (strlen($needle) > strlen($haystack)) {
            return false;
        }
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * String ends with.
     * 
     * @param   string  $haystack   Source string.
     * @param   string  $needle     What we're looking for.
     * @return  bool
     */
    function str_ends_with(string $haystack, string $needle) : bool
    {
        if (strlen($needle) > strlen($haystack)) {
            return false;
        }
        return (substr($haystack, -strlen($needle)) === $needle);
    }
}

if (!function_exists('str_contains')) {
    /**
     * String contains.
     * 
     * @param   string  $haystack   Source string.
     * @param   string  $needle     What we're looking for.
     * @return  bool
     */
    function str_contains(string $haystack, string $needle) : bool
    {
        if (strlen($needle) > strlen($haystack)) {
            return false;
        }
        return (false !== strpos($haystack, $needle));
    }
}

if (!function_exists('str_contains_any')) {
    /**
     * String contains any.
     * 
     * @param   string  $haystack   Source string.
     * @param   array   $needles    What we're looking for.
     * @return  bool
     */
    function str_contains_any(string $haystack, array $needles) : bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('str_contains_all')) {
    /**
     * String contains all.
     * 
     * @param   string  $haystack   Source string.
     * @param   array   $needles    What we're looking for.
     * @return  bool
     */
    function str_contains_all(string $haystack, array $needles) : bool
    {
        foreach ($needles as $needle) {
            if (!str_contains($haystack, $needle)) {
                return false;
            }
        }
        return true;
    }
}
