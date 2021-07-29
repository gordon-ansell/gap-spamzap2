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
 * Slugify filter.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Slugify extends AbstractFilter implements FilterInterface
{
    /**
     * Perform the filter.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  mixed                   Filtered data. 
     */
    public function filter($data)
    {
        // Replace spaces.
        $rep = (!is_null($this->options) and isset($this->options['repspace'])) ? $this->options['repspace'] : '';
        $data = str_replace(' ', $rep, $data);

	  	// Replace non letter or digits by -
	  	$data = preg_replace('~[^\pL\d]+~u', '-', $data);
	
	  	// Transliterate.
	  	$data = iconv('utf-8', 'us-ascii//TRANSLIT', $data);
	
	  	// Remove unwanted characters.
	  	$data = preg_replace('~[^-\w]+~', '', $data);
	
	  	// Trim.
	  	$data = trim($data, '-');
	
	  	// Remove duplicate -
	  	$data = preg_replace('~-+~', '-', $data);
	
	  	// Lowercase.
	  	$data = strtolower($data);

		return $data;		
    }
}