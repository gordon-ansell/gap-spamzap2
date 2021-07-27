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

use App\Domain\Db\DbModel\AbstractDbModel;

/**
 * String block model.
 */
class StringBlockModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'stringblock';


    /**
     * See if we have a value.
     * 
     * @param   string      $value      Value to check.
     * @return  bool
     */
    public function hasValue(string $value): bool
    {
        $result = $this->getDb()
            ->select($this->tableName)
            ->where('item', $value)
            ->fetchArray();

        return (count($result) > 0);
    }
}
