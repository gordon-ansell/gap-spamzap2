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

use GreenFedora\Finder\FinderInterface;
use GreenFedora\Finder\Filter\Set;
use GreenFedora\Finder\Filter\SetInterface;
use GreenFedora\Finder\FilterFilter;
use GreenFedora\Finder\Filter\FilterInterface;
use GreenFedora\Finder\Exception\RuntimeException;

/**
 * Finds files and subdirectories based in filters.
 */
class Finder implements FinderInterface
{
    /**
     * Flags.
     */
    const POSITIVE = -1;
    const NOMATCH  =  0;
    const NEGATIVE =  1;

    const HAS_NEGATIVE_FILTERS = 1;
    const HAS_POSITIVE_FILTERS = 2;

    const ONLY_HAS_NEGATIVE_FILTERS = 1;
    const ONLY_HAS_POSITIVE_FILTERS = 2;

    const APPLY_TO_FILENAME = 1;
    const APPLY_TO_PATHNAME = 2;

    /**
     * File filters.
     * @var FinderFilterSetInterface[]|null
     */
    protected $file = null;

    /**
     * Directory filters.
     * @var SetInterface[]|null
     */
    protected $dir = null;

    /**
     * Start directory.
     * @var string
     */
    protected $start = null;

    /**
     * The results.
     * @var string[]
     */
    protected $results = [];

    /**
     * Global match type.
     * @var int
     */
    protected $gmt = self::NOMATCH;

    /**
     * Recurse?
     * @var bool
     */
    protected $recurse = true;

    /**
     * Base directory, will be dropped off the front of all tests.
     * @var string
     */
    protected $base = '';

    /**
     * Do we have particular file match types.
     * @var int
     */
    protected $matchTypeFile = 0;

    /**
     * Do we have particular dir match types.
     * @var int
     */
    protected $matchTypeDir = 0;

    /**
     * The regex delimiter.
     * @var string
     */
    protected $delimiter = '~';

    /**
     * Return file info?
     * @var bool
     */
    protected $returnFileinfo = false;
    
    /**
     * Constructor.
     * 
     * @param   string                           $start    Start directory.
     * @param   string                           $base     Base directory.
     * @param   SetInterface|array               $file     File filters.
     * @param   SetInterface|array               $dir      Directory filters.
     * @param   int                              $gmt      Global match type.
     * @param   bool                             $rfi      Return SplFileInfo objects?
     * @param   bool                             $recurse  Recurse subdirectories?
     * @return  void
     */
    public function __construct(string $start, string $base = '', $file = null, $dir = null, 
    int $gmt = Finder::NOMATCH, bool $rfi = false, bool $recurse = true)
    {
        $this->start = rtrim($start, '\/');
        $this->base = rtrim($base, '\/');
        $this->gmt = $gmt;
        $this->returnFileinfo = $rfi;
        $this->recurse = $recurse;

        if (!is_null($file)) {
            if (!is_array($file)) {
                $file = [$file];
            }
            foreach ($file as $item) {
                $this->addFileFilterSet($item);
            }
        }
        if (!is_null($dir)) {
            if (!is_array($dir)) {
                $dir = [$dir];
            }
            foreach ($dir as $item) {
                $this->addDirFilterSet($item);
            }
        }
    }

    /**
     * Set the recurse flag.
     * 
     * @param   bool    $flag   Flag to set.
     * @return  FinderInterface
     */
    public function setRecurse(bool $flag = true): FinderInterface
    {
        $this->recurse = $flag;
        return $this;
    }

    /**
     * Do we have negative file filters.
     * 
     * @return bool
     */
    protected function hasNegativeFileFilters(): bool
    {
        return ((self::HAS_NEGATIVE_FILTERS & $this->matchTypeFile) === self::HAS_NEGATIVE_FILTERS);
    }

    /**
     * Do we have positive file filters.
     * 
     * @return bool
     */
    protected function hasPositiveFileFilters(): bool
    {
        return ((self::HAS_POSITIVE_FILTERS & $this->matchTypeFile) === self::HAS_POSITIVE_FILTERS);
    }

    /**
     * Do we have negative dir filters.
     * 
     * @return bool
     */
    protected function hasNegativeDirFilters(): bool
    {
        return ((self::HAS_NEGATIVE_FILTERS & $this->matchTypeDir) === self::HAS_NEGATIVE_FILTERS);
    }

    /**
     * Do we have positive dir filters.
     * 
     * @return bool
     */
    protected function hasPositiveDirFilters(): bool
    {
        return ((self::HAS_POSITIVE_FILTERS & $this->matchTypeDir) === self::HAS_POSITIVE_FILTERS);
    }

    /**
     * Do we only have negative file filters.
     * 
     * @return bool
     */
    protected function onlyHasNegativeFileFilters(): bool
    {
        return (self::ONLY_HAS_NEGATIVE_FILTERS === $this->matchTypeFile);
    }

    /**
     * Do we only have positive file filters.
     * 
     * @return bool
     */
    protected function onlyHasPositiveFileFilters(): bool
    {
        return (self::ONLY_HAS_POSITIVE_FILTERS === $this->matchTypeFile);
    }

    /**
     * Do we only have negative dir filters.
     * 
     * @return bool
     */
    protected function onlyHasNegativeDirFilters(): bool
    {
        return (self::ONLY_HAS_NEGATIVE_FILTERS === $this->matchTypeDir);
    }

    /**
     * Do we only have positive file filters.
     * 
     * @return bool
     */
    protected function onlyHasPositiveDirFilters(): bool
    {
        return (self::ONLY_HAS_POSITIVE_FILTERS === $this->matchTypeDir);
    }

    /**
     * Run the filter.
     * 
     * @param   bool|null    $fi                 Return a file info?
     * @return  \SplFileInfo[]|string[]     Array of entries. 
     */
    public function filter(?bool $fi = null): array
    {
        if (is_null($fi)) {
            $fi = $this->returnFileinfo;
        }

        if (!file_exists($this->start)) {
            throw new RuntimeException(sprintf("Finder start directory '%s' does not exist.", $this->start));
        }

        $this->doFilter($this->start, $fi);

        return $this->results;;
    }

    /**
     * The core filter.
     * 
     * @param   string      $start      Start directory.
     * @param   bool        $fi         Return a file info?
     * @return  void
     */
    protected function doFilter(string $start, bool $fi = false)
    {
        foreach (new \DirectoryIterator($start) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $gmtFiles = $gmtDirs = $this->gmt;
            if (self::NOMATCH == $gmtDirs) {
                if ($this->onlyHasNegativeDirFilters()) {
                    $gmtDirs = self::POSITIVE;
                } else {
                    $gmtDirs = self::NEGATIVE;
                }
            }
            if (self::NOMATCH == $gmtFiles) {
                if ($this->onlyHasNegativeFileFilters()) {
                    $gmtFiles = self::POSITIVE;
                } else {
                    $gmtFiles = self::NEGATIVE;
                }
            }

            $pn = $fileInfo->getPathname();
            if ('' != $this->base) {
                $pn = str_replace($this->base, '', $fileInfo->getPathname());
                $pn = ltrim($pn, '\/');
            }

            // DIRS.
            if ($fileInfo->isDir() and $this->recurse) {

                $carryOn = true;
                if (!is_null($this->dir)) {
                    foreach ($this->dir as $set) {
                        $result = $set->match($pn);
                        if (Finder::NEGATIVE === $result) {
                            $carryOn = false;
                            if (!$this->hasPositiveDirFilters()) {
                                break;
                            }
                        } else if (Finder::POSITIVE === $result) {
                            $carryOn = true;
                            if (!$this->hasNegativeDirFilters()) {
                                break;
                            }
                        // No match.
                        } else {
                            if (Finder::POSITIVE == $gmtDirs) {
                                $carryOn = true;
                            } else {
                                $carryOn = false;
                            }
                        }
                    }
                }
                if ($carryOn) {
                    $this->doFilter($fileInfo->getPathname(), $fi);
                }
            
            // FILES.
            } else if ($fileInfo->isFile()) {

                $fn = $fileInfo->getFilename();

                $carryOn = true;
                if (!is_null($this->file)) {
                    foreach ($this->file as $set) {
                        $result = $set->match($fn);
                        if (Finder::NEGATIVE === $result) {
                            $carryOn = false;
                            if (!$this->hasPositiveFileFilters()) {
                                break;
                            }
                        } else if (Finder::POSITIVE === $result) {
                            $carryOn = true;
                            if (!$this->hasNegativeDirFilters()) {
                                break;
                            }
                        // No match.
                        } else {
                            if (Finder::POSITIVE == $gmtFiles) {
                                $carryOn = true;
                            } else {
                                $carryOn = false;
                            }
                        }
                    }
                }
                if ($carryOn) {
                    if ($fi) {
                        array_unshift($this->results, new \SplFileInfo($fileInfo->getPathname()));
                    } else {
                        array_unshift($this->results, $fileInfo->getPathname());
                    }
                }

            }
        }
    }

    /**
     * Add a file filter set.
     * 
     * @param   SetInterface     $filterSet      Filter set.
     * @param   bool                         $prepend        Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterSet(SetInterface $filterSet, bool $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->file, $filterSet);
        } else {
            $this->file[] = $filterSet;
        }

        if ($filterSet->isNegativeMatch()) {
            $this->matchTypeFile |= self::HAS_NEGATIVE_FILTERS;
        } else {
            $this->matchTypeFile |= self::HAS_POSITIVE_FILTERS;
        }

        return $this;
    }

    /**
     * Add a dir filter set.
     * 
     * @param   SetInterface                 $filterSet      Filter set.
     * @param   bool                         $prepend        Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterSet(SetInterface $filterSet, bool $prepend = false)
    {
        if ($prepend) {
            array_unshift($this->dir, $filterSet);
        } else {
            $this->dir[] = $filterSet;
        }

        if ($filterSet->isNegativeMatch()) {
            $this->matchTypeDir |= self::HAS_NEGATIVE_FILTERS;
        } else {
            $this->matchTypeDir |= self::HAS_POSITIVE_FILTERS;
        }

        return $this;
    }

    /**
     * Add a file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   int                     $flag        Flags.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilter(FilterInterface $filter, int $flags, bool $prepend = false): FinderInterface
    {
        return $this->addFileFilterSet(new Set($filter, $flags), $prepend);
    }

    /**
     * Add a positive file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterPositive(FilterInterface $filter, bool $prepend = false): FinderInterface
    {
        return $this->addFileFilter($filter, self::POSITIVE, $prepend);
    }

    /**
     * Add a negative file filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addFileFilterNegative(FilterInterface $filter, bool $prepend = false): FinderInterface
    {
        return $this->addFileFilter($filter, self::NEGATIVE, $prepend);
    }

    /**
     * Add a dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   int                     $flag        Flags.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilter(FilterInterface $filter, int $flags, bool $prepend = false): FinderInterface
    {
        return $this->addDirFilterSet(new Set($filter, $flags), $prepend);
    }

    /**
     * Add a positive dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterPositive(FilterInterface $filter, bool $prepend = false): FinderInterface
    {
        return $this->addDirFilter($filter, self::POSITIVE, $prepend);
    }

    /**
     * Add a negative dir filter.
     * 
     * @param   FilterInterface         $filter      Filter.
     * @param   bool                    $prepend     Prepend it?
     * @return  FinderInterface
     */
    public function addDirFilterNegative(FilterInterface $filter, bool $prepend = false): FinderInterface
    {
        return $this->addDirFilter($filter, self::NEGATIVE, $prepend);
    }
}
