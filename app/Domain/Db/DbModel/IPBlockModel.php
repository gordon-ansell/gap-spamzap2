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
 * IP block model.
 */
class IPBlockModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'ipblock';

    /**
     * Create an entry.
     * 
     * @param   array         $data       Data to create record with.
     * @return  mixed                     Return the insert ID.
     * @throws  DbModelException
     */
    public function create(array $data)
    {
        $ip = $data['ip'];
        if (false === strpos($ip, '/')) {
            $data['iplong'] = ip2long($ip);
            $data['range_start'] = $data['range_end'] = $ip;
            $data['range_start_long'] = $data['range_end_long'] = $data['iplong'];
        } else {
            $sp = explode('/', $ip);
            $range = IPAddress::cidrToRange($ip);
            $data['iplong'] = ip2long($sp[0]);
            $data['range_start'] = $range[0];
            $data['range_end'] = $range[1];
            $data['range_start_long'] = ip2long($range[0]);
            $data['range_end_long'] = ip2long($range[1]);
            $data['subnet'] = intval($sp[1]);
        }

        return parent::create($data);
    }

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
     * Is covered?
     * 
     * @param   string                  $ip         IP address to test.
     * @param   bool                    $simple     Simple return?
     * @return  string|null                         Null if it isn't covered, otherwise string message.
     */
    public function isCovered(string $ip, bool $simple = false): ?string
    {
        $range_start_long = 0;
        $range_end_long = 0;
        if (false === strpos($ip, '/')) {
            $range_start_long = $range_end_long = ip2long($ip);
        } else {
            $range = IPAddress::cidrToRange($ip);
            $range_start_long = ip2long($range[0]);
            $range_end_long = ip2long($range[1]);
        }

        $select = $this->getDb()->select($this->tableName);
        $select
            ->where('range_start_long', $range_start_long, '<=')
            ->where('range_end_long', $range_end_long, '>=');

        $result = $select->fetchArray();

        if (0 === count($result)) {
            return null;
        }

        if ($simple) {
            return $result[0]['ip'];
        } else {
            $key = $this->tableName . '_id';
            $ret = '';
            foreach ($result as $single) {
                $entry = '(' . $single[$key] . ') ' . $single['ip'];
                $entry .= ' ' . $this->convDt($single['dt']);

                if ('' !== $ret) {
                    $ret .= ', ';
                }
                $ret .= $entry;
            }
            return $ret;
        }
    }

    /**
     * Is overriding?
     * 
     * @param   string          $ip         IP address to test.
     * @return  array|null                  Null if we're not overriding anything, otherwise an array of IDs we override.
     */
    public function isOverriding(string $ip): ?array
    {
        $range_start_long = 0;
        $range_end_long = 0;
        if (false === strpos($ip, '/')) {
            $range_start_long = $range_end_long = ip2long($ip);
        } else {
            $range = IPAddress::cidrToRange($ip);
            $range_start_long = ip2long($range[0]);
            $range_end_long = ip2long($range[1]);
        }

        $select = $this->getDb()->select($this->tableName);
        $select->where('range_start_long', $range_start_long, '>=')
            ->where('range_end_long', $range_end_long, '<=');

        $result = $select->fetchArray();

        if (0 === count($result)) {
            return null;
        }

        $ret = [];
        $key = $this->tableName . '_id';
        foreach ($result as $single) {
            $ret[] = array($single[$key], $single['ip'], $single['dt']);
        }
        return $ret;
    }
}
