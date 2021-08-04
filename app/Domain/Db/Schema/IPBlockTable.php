<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Domain\Db\Schema;

use GreenFedora\Db\Schema\Table;

/**
 * IP block table.
 */
class IPBlockTable extends Table
{
    /**
     * Initialisation.
     *
     * @return  void
     */
    protected function init()
    {
        $this->addColumnPrimaryAuto()
            ->addColumnText('dt')
            ->addColumnText('ip')
            ->addColumnInt('iplong')
            ->addColumnSmallInt('subnet', 32)
            ->addColumnText('range_start')
            ->addColumnText('range_end')
            ->addColumnBigInt('range_start_long')
            ->addColumnBigInt('range_end_long')
            ->addColumnText('desc', '');
    }

    /**
     * Called after a successful create.
     *
     * @return  void
     */
    protected function postCreate()
    {
    }
}
