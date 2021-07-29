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

use GreenFedora\Finder\Filter\Filter;
use GreenFedora\Finder\Finder;

/**
 * A file name contains a string.
 */
class FileNameContains extends Filter
{
    /**
     * Constructor.
     * 
     * @param   string|array    $patterns   Patterns.
     * @param   int             $applyTo    Apply to?
     * @param   string          $delimiter  Regex delimiter.
     * @return  void
     */
    public function __construct($patterns, int $applyTo = Finder::APPLY_TO_FILENAME, string $delimiter = '~')
    {
        $regex = ".*(?:";
        if (is_array($patterns)) {
            $regex .= implode('|', $patterns);
        } else {
            $regex .= $patterns;
        }
        $regex .= ")";
        parent::__construct($regex, $applyTo, $delimiter);
    }
}
