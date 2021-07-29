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

/**
 * Interface for dotted arrays.
 */
interface DottedArrInterface extends ArrInterface
{
    /**
     * See if we have a particular dotted key.
     *
     * @param   string      $key            Key to test.
     * @return  bool
     */
    public function hasDotted(string $key): bool;

    /**
     * Get a dotted key.
     *
     * @param   string      $dotted         Dotted key.
     * @param   mixed       $default        Default if not found.
     * @return  mixed
     */
    public function dotted(string $key, $default = null);

    /**
     * Set a dotted key.
     *
     * @param   string      $key            Dotted key.
     * @param   mixed       $value        	Value to set.
     * @return  void
     */
    public function setDotted(string $key, $value): void;

    /**
     * Unset a dotted key.
     *
     * @param   string      $key            Dotted key.
     * @return  void
     */
    public function unsetDotted(string $key): void;
}
