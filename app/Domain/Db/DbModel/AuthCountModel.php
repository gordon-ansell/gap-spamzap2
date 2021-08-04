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
use GreenFedora\IP\IPAddress;

/**
 * Auth count model.
 */
class AuthCountModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'authcount';

    /**
     * See if we have an IP.
     * 
     * @param   string      $value      Value to check.
     * @return  bool
     */
    public function hasIP(string $value): bool
    {
        $result = $this->getDb()
            ->select($this->tableName)
            ->where('ip', $value)
            ->fetchArray();

        return (count($result) > 0);
    }

    /**
     * Increment the count.
     * 
     * @param   string  $ip     IP to check.
     * @param   string  $dt     Latest.
     * 
     * @return  mixed
     */
    public function incrementCount(string $ip, string $dt)
    {
        if ($this->hasIp($ip)) {
            $result = $this->getDb()
                ->select($this->tableName)
                ->where('ip', $ip)
                ->fetchArray();

            if (count($result) > 0) {
                $curr = intval($result[0]['ipcount']);
                return $this->update($result[0][$this->tableName . '_id'], ['ipcount' => ++$curr, 'latest' => $dt]);
            }
        } else {
            $data = [
                'ip' => $ip,
                'iplong' => ip2long($ip),
                'ipcount' => 1,
                'latest' => $dt,
            ];
            return $this->create($data);
        }

    }
}
