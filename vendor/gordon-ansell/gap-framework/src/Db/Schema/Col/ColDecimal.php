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
namespace GreenFedora\Db\Schema\Col;

use GreenFedora\Db\Schema\Col;
use GreenFedora\Db\Schema\ColInterface;
use GreenFedora\Db\Schema\TableInterface;
use GreenFedora\Db\DbInterface;

/**
 * Database decimal schema column.
 */
class ColDecimal extends Col implements ColInterface
{
    /**
     * Constructor.
     *
     * @param   DbInterface         $db         Database entry point.
     * @param   TableInterface      $table      Parent table.
     * @param   string              $name       Column name.
     * @param   int                 $length     Length.
     * @param   int                 $decimals   Decimals.
     * @param   float               $default    Default value,
     * @param   array               $props      Column properties.
     * @return  void
     */
    public function __construct(DbInterface $db, TableInterface $table, string $name, int $length, int $decimals = 2, 
        float $default = 0.0, array $props = array())
    {
        $props = array_replace_recursive($props, array('default' => $default, 'length' => $length, 'decimals' => $decimals));
        parent::__construct($db, $table, $name, 'DECIMAL', $props);
    }
}
