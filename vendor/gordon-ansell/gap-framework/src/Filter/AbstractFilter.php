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

/**
 * Abstract filter.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

abstract class AbstractFilter
{
    /**
     * Options.
     * @var iterable
     */
    protected $options = null;

    /**
     * Constructor.
     * 
     * @param   iterable    $options    Validation options.
     * @return  void 
     */
    public function __construct(?iterable $options = null)
    {
        $this->options = $options;
    }

    /**
     * Perform the filter.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  mixed                   Filtered data. 
     */
    abstract public function filter($data);
}