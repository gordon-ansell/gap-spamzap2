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
use GreenFedora\Finder\Finder;
use RuntimeException;

/**
 * A filter for the Finder.
 */
class Filter implements FilterInterface
{
    /**
     * The regex filter.
     * @var string|null
     */
    protected $regex = null;

    /**
     * The delimiter.
     * @var string
     */
    protected $delimiter = '~';

    /**
     * Apply this to?
     * @var int
     */
    protected $applyTo = 0;

    /**
     * Constructor.
     * 
     * @param   string      $regex      Regular expression.
     * @param   int         $applyTo    Apply to?
     * @param   string      $delimiter  Regex delimiter.
     * @return  void
     */
    public function __construct(string $regex, int $applyTo = Finder::APPLY_TO_PATHNAME, string $delimiter = '~')
    {
        $this->regex = $regex;
        $this->applyTo = $applyTo;
        $this->delimiter = $delimiter;
    }

    /**
     * Match something against the regex.
     * 
     * @param   string      $source     The source we're matching.
     * @return  bool
     * @throws  RuntimeException
     */
    public function match(string $source): bool
    {
        $result = preg_match($this->delimiter . $this->regex . $this->delimiter, $source);
        if (false === $result) {
            throw new RuntimeException(sprintf("Error with regex match, matching >%s< against >%s<.", $source, $this->regex));
        }
        return (1 === $result);
    }

    /**
     * Get the apply to flag.
     * 
     * @return  int
     */
    public function applyTo(): int
    {
        return $this->applyTo;
    }
}
