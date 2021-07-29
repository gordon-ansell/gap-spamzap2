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
namespace GreenFedora\Tests\Db\Schema;

use GreenFedora\Db\Schema\Table;

/**
 * IP table.
 */
class Table1 extends Table
{
    /**
     * Initialisation.
     *
     * @return  void
     */
    protected function init()
    {
        $this->addColumnPrimaryAuto()
            ->addColumnDateTime('dt')
            ->addColumnFk('table2')
            ->addColumnBigInt('iplong')
            ->addColumnText('raw');
    }
}
