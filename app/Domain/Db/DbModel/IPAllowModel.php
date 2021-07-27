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
namespace App\Domain\Db\DbModel;

use App\Domain\Db\DbModel\IPBlockModel;

/**
 * IP allow model.
 */
class IPAllowModel extends IPBlockModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'ipallow';

}
