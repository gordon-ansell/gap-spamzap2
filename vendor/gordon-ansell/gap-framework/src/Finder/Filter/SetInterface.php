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

use GreenFedora\Finder\Filter\FilterInterface;

/**
 * Interface for the Finder class.
 */
interface SetInterface
{
    /**
     * Do the match.
     * 
     * @param   string  $target     Target to match.
     * @return  int                 0 = no match, 1 = positive match, -1 = negative match.
     */
    public function match($target): int;

    /**
     * Get the filter.
     * 
     * @return  FilterInterface
     */
    public function getFilter(): FilterInterface;

    /**
     * Get the match type.
     * 
     * @return int
     */
    public function getMatchType(): int;

    /**
     * Is this a negative match filter?
     * 
     * @return  bool
     */
    public function isNegativeMatch(): bool;
}
