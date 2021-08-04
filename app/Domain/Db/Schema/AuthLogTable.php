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
 * Auth log table.
 */
class AuthLogTable extends Table
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
            ->addColumnText('username')
            ->addColumnInt('userid', 0, ['null' => true])
            ->addColumnText('pwd')
            ->addColumnText('ip')
            ->addColumnTinyInt('userexists', 0);
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
