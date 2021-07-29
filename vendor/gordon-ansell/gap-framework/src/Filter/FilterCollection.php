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
namespace GreenFedora\Filter;

use GreenFedora\Filter\FilterInterface;

/**
 * Filter collection.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class FilterCollection
{
    /**
     * Filters.
     * @var array
     */
    protected $filters = [];

    /**
     * Constructor.
     * 
     * @param   array    $filters     Filters to add.
     * @return  void 
     */
    public function __construct(?array $filters = null)
    {
        if (null !== $filters) {
            foreach ($filters as $filter) {
                $this->add($filter);
            }
        }
    }

    /**
     * Add a filter.
     * 
     * @param   FilterInterface  $filter  Validator to add.
     * @return  self
     */
    public function add(FilterInterface $filter): self
    {
        $this->filter[] = $filter;
        return $this;
    }

    /**
     * Perform the filter.
     * 
     * @param   mixed       $data       Data to filter.
     * @return  mixed                   Filtered data. 
     */
    public function filter($data)
    {
        foreach ($this->filters as $filter) {
            $data = $filter->filter($data);
        }
        return $data;
    }
}