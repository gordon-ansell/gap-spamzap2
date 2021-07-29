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
 * Database datetime schema column.
 */
class ColDateTime extends Col implements ColInterface
{
    /**
     * Constructor.
     *
     * @param   DbInterface         $db         Database entry point.
     * @param   TableInterface      $table      Parent table.
     * @param   string              $name       Column name.
     * @param   array               $props      Column properties.
     * @return  void
     */
    public function __construct(DbInterface $db, TableInterface $table, string $name, array $props = array())
    {
        parent::__construct($db, $table, $name, 'DATETIME', $props);
    }
}
