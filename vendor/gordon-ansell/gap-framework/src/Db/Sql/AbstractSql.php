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
namespace GreenFedora\Db\Sql;

use GreenFedora\Db\DbInterface;

use GreenFedora\Finder\Filter\FileNameContains;
use GreenFedora\Finder\Filter\Set;
use GreenFedora\Finder\Finder;
use GreenFedora\Stdlib\Path;


/**
 * Abstract SQL class.
 */
abstract class AbstractSql
{
    /**
     * Database parent.
     * @var DbInterface
     */
    protected $db = null;

    /**
     * Cache path.
     * @var string
     */
    protected $cachePath = null;

    /**
     * Constructor.
     *
     * @param   DbInterface      $db         Database parent.
     * @return  void
     */
    public function __construct(DbInterface $db)
    {
        $this->db = $db;
        $this->prepareForCaching();
    }

    /**
     * Prepare for caching?
     * 
     * @return  void
     */
    protected function prepareForCaching()
    {
        if ($this->db->getConfig()->has('cache')) {
            $cd = $this->db->getConfig()->get('cache');
            if (!file_exists($cd)) {
                mkdir($cd, 0777, true);
            }
            $this->cachePath = $cd;
        }
    }

    /**
     * Clear cache for this table.
     * 
     * @param   string  $table  Table to clear cache for.
     * @return  void
     */
    protected function clearCache(string $table)
    {
        $finder = new Finder($this->cachePath, $this->cachePath, new Set(new FileNameContains('.' . $table), Finder::POSITIVE));
        $results = $finder->filter(false);
        if (0 == count($results)) {
            logger()->debug(sprintf("No cache files to clear for %s", $table), null, "DB:CACHE");
            return;
        }
        foreach ($results as $item) {
            //$fn = Path::join($this->cachePath, $item);
            unlink($item);
            logger()->debug(sprintf("Deleted DB cache file: %s", $item), null, "DB:CACHE");
        }

    }
}
