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
namespace GreenFedora\Finder\Filter;

/**
 * Interface for the Finder class.
 */
interface FilterInterface
{
    /**
     * Match something against the regex.
     * 
     * @param   string      $source     The source we're matching.
     * @return  bool
     */
    public function match(string $source): bool;

    /**
     * Get the apply to flag.
     * 
     * @return  int
     */
    public function applyTo(): int;
}
