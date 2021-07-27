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
namespace App\Domain;

use GreenFedora\IP\IPAddress;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\IP\WhoIsParser;
use App\Domain\Exception\InvalidArgumentException;

use phpWhois\Utils;

/**
 * IP lookup class.
 */
class DoLookup
{
    /**
     * IP address.
     * @var string
     */
    protected $ip = null;

    /**
     * Include raw?
     * @var bool
     */
    protected $includeRaw = false;

    /**
     * Storage for looked up data.
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     * 
     * @param   string      $ip             IP address to lookup.
     * @param   bool        $includeRaw     Include raw data?
     * @param   bool        $autoLookup     Do the lookup from the constructor?
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct(string $ip, bool $includeRaw = false, bool $autoLookup = true)
    {
        if (!IPAddress::validate($ip)) {
            throw new InvalidArgumentException(sprintf("IP address invalid: '%s'.", $ip));
        }
        $this->ip = $ip;
        $this->includeRaw = $includeRaw;
        if ($autoLookup) {
            $this->lookup();
        }
    }

    /**
     * Do the lookup.
     * 
     * @return  array                   Looked up data.
     */
    public function lookup(): array
    {
		$whois = new Utils();
		$result = $whois->lookup($this->ip);

		$current = array(
			'name'              =>  '', 
			'ip'                =>  $this->ip, 
			'iplong'            =>  ip2long($this->ip), 
			'cidrs'             =>  array(),
			'netname'           =>  '',
		);

		$p = new WhoIsParser($result, $this->ip);

		$current['name'] = $p->getName();
		$current['address'] = $p->getAddress();
		$current['country'] = $p->getCountry();
		$current['netname'] = $p->getNetwork();
		$current['domain'] = $p->getDomain();
        $current['networkstatus'] = $p->getNetworkStatus();
		list($current['range'], $current['range_start'], $current['range_end'], $current['cidrs']) = $p->getRange();

		$current['raw'] = $whois->showObject($result);

        $this->data = $current;
		return $current;
    }

    /**
     * Get the looked up data.
     * 
     * @param   bool|null    $includeRaw     Include raw data?
     * 
     * @return  array
     */
    public function getData(bool $includeRaw = null): array
    {
        if (null === $includeRaw) {
            $includeRaw = $this->includeRaw;
        }
        
        if ($includeRaw) {
            return $this->data;
        } else {
            $ret = new Arr($this->data);
            unset($ret['raw']);
            return $ret->toArray();
        }
    }

}