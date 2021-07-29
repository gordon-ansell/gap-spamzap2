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
namespace GreenFedora\Session;

/**
 * Session handler interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface SessionInterface
{
    /**
     * Get the session prefix.
     * 
     * @return  string
     */
    public function getPrefix() : string;

    /**
     * Regenerate a session ID.
     *
     * @return  void
     */
    public function regenerateId();

    /**
     * Clear all session data.
     *
     * @return  SessionInterface
     */
    public function clear() : self;

    /**
     * Unset a session variable.
     *
     * @param   string          $key        Key to unset.
     * @return  SessionInterface
     */
    public function unset(string $key) : self;

    /**
     * Generate a csrf token.
     * 
     * @return  string
     */
    public function csrfToken1() : string;

    /**
     * Generate a csrf token.
     * 
     * @param   string      $name       Name to use.
     * @return  string
     */
    public function csrfToken2(string $name) : string;

    /**
     * Set a session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $val        Value to set.
     * @param   bool            $flash      Flash data?
     * @return  SessionInterface
     */
    public function set(string $key, $val, bool $flash = false) : self;

    /**
     * Set a flash session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $val        Value to set.
     * @return  SessionInterface
     */
    public function setFlash(string $key, $val) : self;

    /**
     * Get a session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $def        Default to get if not set.
     * @param   bool            $clearFlash Clear down flash data if this is a flash entry.
     * @return  mixed
     */
    public function get(string $key, $def = null, bool $clearFlash = true);

    /**
     * Get all the session keys unprefixed.
     * 
     * @param   string          $pref       Prefix.
     * @return  array
     */
    public function getAllUnprefixed(string $pref = ''): array;
    
    /**
     * See if we have a session variable.
     *
     * @param   string          $key        Key to check.
     * @return  bool
     */
    public function has(string $key) : bool;
}
