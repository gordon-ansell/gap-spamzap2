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

use GreenFedora\Filter\AbstractFilter;
use GreenFedora\Filter\FilterInterface;

/**
 * Stripslashes filter.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Stripslashes extends AbstractFilter implements FilterInterface
{
    /**
     * Perform the filter.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  mixed                   Filtered data. 
     */
    public function filter($data)
    {
        return stripslashes($data);
    }
}