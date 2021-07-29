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

use GreenFedora\Finder\Filter\SetInterface;
use GreenFedora\Finder\Filter\FilterInterface;
use GreenFedora\Finder\Finder;

/**
 * A filter and its match type.
 */
class Set implements SetInterface
{
    /**
     * Filter.
     * @var FilterInterface
     */
    protected $filter = null;

    /**
     * Match type.
     * @var int
     */
    protected $matchType = null;

    /**
     * Constructor.
     * 
     * @param   FilterInterface             $filter     Filter.
     * @param   int                         $matchType  Type of match.
     * @return  void
     */
    public function __construct(FilterInterface $filter, int $matchType)
    {
        $this->filter = $filter;
        $this->matchType = $matchType;
    }

    /**
     * Do the match.
     * 
     * @param   string  $target     Target to match.
     * @return  int                 0 = no match, 1 = positive match, -1 = negative match.
     */
    public function match($target): int
    {
        $result = $this->filter->match($target);
        if (false === $result) {
            return Finder::NOMATCH;
        } else {
            return ($this->isNegativeMatch()) ? Finder::NEGATIVE : Finder::POSITIVE;
        }
    }

    /**
     * Get the filter.
     * 
     * @return  FilterInterface
     */
    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }

    /**
     * Get the match type.
     * 
     * @return int
     */
    public function getMatchType(): int
    {
        return $this->matchType;
    }

    /**
     * Is this a negative match filter?
     * 
     * @return  bool
     */
    public function isNegativeMatch(): bool
    {
        return (Finder::NEGATIVE === $this->matchType);
    }

}
