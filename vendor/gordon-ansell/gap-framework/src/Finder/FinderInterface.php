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
namespace GreenFedora\Finder;

use GreenFedora\Finder\Filter\SetInterface;
use GreenFedora\Finder\Filter\FilterInterface;

/**
 * Interface for the Finder class.
 */
interface FinderInterface
{
    /**
     * Set the recurse flag.
     * 
     * @param   bool    $flag   Flag to set.
     * @return  FinderInterface
     */
    public function setRecurse(bool $flag = true): FinderInterface;

    /**
     * Run the filter.
     * 
     * @param   bool    $fi                 Return a file info?
     * @return  \SplFileInfo[]|string[]     Array of entries. 
     */
    public function filter(bool $fi = false): array;

    /**
     * Add a file filter set.
     * 
     * @param   SetInterface                 $filterSet      Filter set.
     * @param   bool                         $prepend        Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterSet(SetInterface $filterSet, bool $prepend = false);

    /**
     * Add a dir filter set.
     * 
     * @param   SetInterface                 $filterSet      Filter set.
     * @param   bool                         $prepend        Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterSet(SetInterface $filterSet, bool $prepend = false);

    /**
     * Add a file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   int                     $flag        Flags.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilter(FilterInterface $filter, int $flags, bool $prepend = false): FinderInterface;

    /**
     * Add a positive file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterPositive(FilterInterface $filter, bool $prepend = false): FinderInterface;

    /**
     * Add a negative file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterNegative(FilterInterface $filter, bool $prepend = false): FinderInterface;

    /**
     * Add a dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   int                     $flag        Flags.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilter(FilterInterface $filter, int $flags, bool $prepend = false): FinderInterface;

    /**
     * Add a positive dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterPositive(FilterInterface $filter, bool $prepend = false): FinderInterface;

    /**
     * Add a negative dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterNegative(FilterInterface $filter, bool $prepend = false): FinderInterface;
}
