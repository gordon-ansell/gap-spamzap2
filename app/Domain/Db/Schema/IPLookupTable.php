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
 * IP lookup table.
 */
class IPLookupTable extends Table
{
    /**
     * Initialisation.
     *
     * @return  void
     */
    protected function init()
    {
        $this->addColumnVarCharPrimary(15)
            ->addColumnText('ipl_dt')
            ->addColumnText('ipl_cidrs')
            ->addColumnText('ipl_name')
            ->addColumnText('ipl_netname')
            ->addColumnText('ipl_address')
            ->addColumnText('ipl_country')
            ->addColumnText('ipl_domain')
            ->addColumnText('ipl_networkstatus');
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
