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
use App\Domain\DoLookup;

/**
 * IP lookup model.
 */
class IPLookupModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'iplookup';

    /**
     * Is the ID a string?
     * @vard bool
     */
    protected $idIsString = true;

    /**
     * Create an entry.
     * 
     * @param   string        $ip         IP address.
     * @param   bool          $ow         Overwrite?
     * @return  mixed                     Return the insert ID.
     * @throws  DbModelException
     */
    public function addLookup(string $ip, bool $ow = false)
    {
        // See if an entry already exists.
        if ($this->hasEntry($ip)) {
            if (false === $ow) {
                return $ip;
            } else {
                $this->delete($ip);
            }
        }

        // Lookup the address.
        $lu = new DoLookup($ip);
        $temp = $lu->getData();

        $data = [
            'iplookup_id'           =>  $ip,
            'ipl_dt'                =>  $this->getDt(),
            'ipl_cidrs'             =>  implode(',', $temp['cidrs']),
            'ipl_name'              =>  $temp['name'],
            'ipl_netname'           =>  $temp['netname'],
            'ipl_address'           =>  $temp['address'],
            'ipl_country'           =>  $temp['country'],
            'ipl_domain'            =>  $temp['domain'],
            'ipl_networkstatus'     =>  $temp['networkstatus'],
        ];

        return $this->create($data);
    }

}
