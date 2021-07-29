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
namespace GreenFedora\IP;

use GreenFedora\IP\Exception\OutOfBoundsException;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\IP\IPAddress;
use GreenFedora\IP\WhoIsParserInterface;

/**
 * WhoIs Parser.
 */
class WhoIsParser implements WhoIsParserInterface
{
    /**
     * Input data.
     * @var array
     */
    protected $input = array();

    /**
     * Admin data.
     * @var array
     */
    protected $admin = null;

    /**
     * Owner data.
     * @var array
     */
    protected $owner = null;

    /**
     * Network data.
     * @var array
     */
    protected $network = null;

    /**
     * IP address.
     * @var string
     */
    protected $ip = null;

    /**
     * Constructor.
     * 
     * @param   array       $input      Array of whois data.
     * @param   string      $ip         IP address.
     * @return  void
     * @throws  OutOfBoundsException
     */
    public function __construct(array $input, string $ip = null)
    {
        $this->input = $input;
        $this->ip = $ip;

        if (!array_key_exists('regrinfo', $input)) {
            throw new OutOfBoundsException("No 'regrinfo' in WhoIs data");
        }

        $regrinfo = $input['regrinfo'];

        if (array_key_exists('admin', $regrinfo)) {
            $admin = $regrinfo['admin'];
            if (is_array($admin) and Arr::isArraySequential($admin)) {
                $admin = $admin[0];
            }
            $this->admin = $admin;
        }

        if (array_key_exists('owner', $regrinfo)) {
            $owner = $regrinfo['owner'];
            if (is_array($owner) and Arr::isArraySequential($owner)) {
                $owner = $owner[0];
            }
            $this->owner = $owner;
        }

        if (array_key_exists('network', $regrinfo)) {
            $network = $regrinfo['network'];
            if (is_array($network) and Arr::isArraySequential($network)) {
                $network = $network[0];
            }
            $this->network = $network;
        }
    } 

    /**
     * =====================================
     * OWNER
     * =====================================
     */

    /**
     * Get an owner value.
     * 
     * @param   string      $key        Admin key.
     * @return  mixed
     */
    protected function getOwnerValue(string $key)
    {
        if (null === $this->owner) {
            return '';
        }

        if (!array_key_exists($key, $this->owner)) {
            return '';
        }

        return $this->owner[$key];
    }

    /**
     * Get the owner organization.
     * 
     * @return  string
     */
    public function getOwnerOrganization() : string
    {
        $org = $this->getOwnerValue('organization');

        if (is_array($org)) {
            if (false === strpos($org[0], '***********')) {
                return implode(', ', $org);
            } else {
                return '';
            }
        } else {
            return $org;
        }
    }

    /**
     * Get the owner name.
     * 
     * @return  string
     */
    public function getOwnerName() : string
    {
        $name = $this->getOwnerValue('name');

        if (is_array($name)) {
            return implode(', ', $name);
        } else {
            return $name;
        }
    }

    /**
     * Get the owner handle.
     * 
     * @return  string
     */
    public function getOwnerHandle() : string
    {
        $handle = $this->getOwnerValue('handle');

        if (is_array($handle)) {
            return implode(', ', $handle);
        } else {
            return $handle;
        }
    }

    /**
     * Get the owner address.
     * 
     * @return  string|array
     */
    public function getOwnerAddress($asArray = true) 
    {
        $address = $this->getOwnerValue('address');

        if ('' == $address) {
            return ($asArray) ? array() : '';
        }

        //echo "<pre>";
        //var_dump($address);
        //echo "</pre>";

        if (is_array($address)) {
            return ($asArray) ? $address : Arr::implode(', ', $address);
        } else {
            return ($asArray) ? array($address) : $address;
        }
    }

    /**
     * Get the owner country.
     * 
     * @return  string
     */
    public function getOwnerCountry() 
    {
        $address = $this->getOwnerAddress();

        if (is_array($address) and array_key_exists('country', $address)) {
            return strtoupper($address['country']);
        }
        return '';
    }

    /**
     * =====================================
     * ADMIN
     * =====================================
     */

    /**
     * Get an admin value.
     * 
     * @param   string      $key        Admin key.
     * @return  mixed
     */
    protected function getAdminValue(string $key)
    {
        if (null === $this->admin) {
            return '';
        }

        if (!array_key_exists($key, $this->admin)) {
            return '';
        }

        return $this->admin[$key];
    }

    /**
     * Get the admin name.
     * 
     * @return  string
     */
    public function getAdminName() : string
    {
        return $this->getAdminValue('name');
    }

    /**
     * Get the admin role.
     * 
     * @return  string
     */
    public function getAdminRole() : string
    {
        return $this->getAdminValue('role');
    }

    /**
     * Get the admin address.
     * 
     * @return  string|array
     */
    public function getAdminAddress($asArray = true) 
    {
        $address = $this->getAdminValue('address');

        if ('' == $address) {
            return ($asArray) ? array() : '';
        }

        if (is_array($address)) {
            return ($asArray) ? $address : Arr::implode(', ', $address);
        } else {
            return ($asArray) ? array($address) : $address;
        }
    }

    /**
     * Get the admin address line 1.
     * 
     * @return  string
     */
    public function getAdminAddressLine1() 
    {
        $address = $this->getAdminAddress();

        if (isset($address[0])) {
            return $address[0];
        }

        return '';
    }


    /**
     * =====================================
     * NETWORK
     * =====================================
     */

    /**
     * Get an admin value.
     * 
     * @param   string      $key        Admin key.
     * @return  mixed
     */
    protected function getNetworkValue(string $key)
    {
        if (null === $this->network) {
            return '';
        }

        if (!array_key_exists($key, $this->network)) {
            return '';
        }

        return $this->network[$key];
    }

    /**
     * Get the network name.
     * 
     * @return  string
     */
    public function getNetworkName() : string
    {
        return $this->getNetworkValue('name');
    }

    /**
     * Get the network status.
     * 
     * @return  string
     */
    public function getNetworkStatus() : string
    {
        return $this->getNetworkValue('status');
    }

    /**
     * Get the network country.
     * 
     * @return  string
     */
    public function getNetworkCountry() : string
    {
        $c = $this->getNetworkValue('country');

        if ('' != $c) {
            if (is_array($c)) {
                return $c[0];
            } else {
                return $c;
            }
        }

        if (!is_array($this->network)) {
            echo "<pre>";
            var_dump($this->input);
            echo "</pre>";
        }

        if (array_key_exists('nserver', $this->network)) {
            $nserver = $this->network['nserver'];
            if (is_array($nserver)) {
                foreach ($nserver as $key => $val) {
                    $ex = explode('.', $key);
                    return strtoupper($ex[count($ex) - 1]);
                    break;
                }
            }
        }

        return '';
    }

    /**
     * Get the network inetnum.
     * 
     * @return  string
     */
    public function getNetworkInetnum() : string
    {
        return $this->getNetworkValue('inetnum');
    }

    /**
     * Get the network mnt-by.
     * 
     * @return  string
     */
    public function getNetworkMntBy() : string
    {
        $m = $this->getNetworkValue('mnt-by');

        if (is_array($m)) {
            return implode(', ', $m);
        } else {
            return $m;
        }
    }

    /**
     * =====================================
     * EXTRACTIONS
     * =====================================
     */

    /**
     * Append only new values.
     * 
     * @param   string      $existing       Existing value.
     * @param   string      $new            New value.
     * @return  void
     */
    public function appendNew(string &$existing, string $new)
    {
        if ('' != $new) {
            if ('' == $existing) {
                $existing = $new;
            } else if (false === stripos($existing, $new)) {
                $existing .= ', ' . $new;
            }
        }
    }

    /**
     * Get the domain.
     * 
     * @return  string
     */
    public function getDomain() : string
    {
        if (null !== $this->ip) {
            $lu = gethostbyaddr($this->ip);
            if (is_array($lu)) {
                $lu = $lu[0];
            }
            if ($lu != $this->ip) {
                return $lu;
            }
        }
        return '';
    }

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName() : string
    {
        $name = $this->getAdminName();
        $this->appendNew($name, $this->getAdminRole());
        $this->appendNew($name, $this->getOwnerName());
        $this->appendNew($name, $this->getOwnerOrganization());
        $this->appendNew($name, $this->getOwnerHandle());
        $this->appendNew($name, $this->getAdminAddressLine1());
        $this->appendNew($name, $this->getNetworkName());
        $this->appendNew($name, $this->getNetworkStatus());
        $this->appendNew($name, $this->getDomain());

        return $name;
    }

    /**
     * Get the network name.
     * 
     * @return  string
     */
    public function getNetwork() : string
    {
        $name = $this->getNetworkName();
        $this->appendNew($name, $this->getNetworkMntBy());
        $this->appendNew($name, $this->getNetworkStatus());

        return $name;
    }

    /**
     * Get the country.
     * 
     * @return  string
     */
    public function getCountry() : string
    {
        $country = $this->getOwnerCountry();

        if ('' == $country) {
            $country = $this->getNetworkCountry();
        }

        return $country;
    }

    /**
     * Get the range.
     * 
     * @return  array
     */
    public function getRange() : array
    {
        $range = $this->getNetworkInetnum();

        if (false !== strpos($range, '/')) {
            list($low, $high) = IPAddress::cidrToRange($range);
            $range = $low . ' - ' . $high;
        }

        $rsplit = explode('-', $range);
        $cidrs = IPAddress::rangeToCIDRList(trim($rsplit[0]), trim($rsplit[1]));

        $current = array();

        $current[] = str_replace(' ', '', $range);      // range
        $current[] = trim($rsplit[0]);                  // range_start
        $current[] = trim($rsplit[1]);                  // range_end
        $current[] = $cidrs;                            // cidrs

        return $current;
    }

    /**
     * Get the address.
     * 
     * @return  string
     */
    public function getAddress() : string
    {
        $address = $this->getOwnerAddress(false);
        if ('' == $address) {
            $address = $this->getAdminAddress(false);
        }
        return $address;
    }
}
