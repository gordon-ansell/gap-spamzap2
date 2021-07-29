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

use GreenFedora\IP\IPAddressInterface;

use GreenFedora\IP\Exception\InvalidArgumentException;

/**
 * IP address helper.
 */
class IPAddress implements IPAddressInterface
{
    /**
     * IP.
     * @var string
     */
    protected $ip = null;

    /**
     * Constructor.
     * 
     * @param   string|null     $ip             IP address.
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct(?string $ip = null)
    {
        if (null === $ip) {
            $ip = static::getClientIp();
        }
        $ip = trim($ip);
        if (!static::validate($ip)) {
            throw new InvalidArgumentException(sprintf("'%s' is an invalid IP address", $ip));
        }
        $this->ip = $ip;
    }

    /**
    * Get the client's IP address.
    * 
    * @param    bool    $allowProxy     Allow proxy IP addresses.
    * @return   string                  Client IP address.
    */
    public static function getClientIp(bool $allowProxy = true) : string
    {
        if ($allowProxy) {
            // Shared address (via ISP).
            if (!empty($_SERVER['HTTP_CLIENT_IP']) and static::validate($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            }
            
            // IPs from proxies.
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $GreenHat = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($GreenHat as $ip) {
                    if (static::validate(trim($ip))) {
                        return trim($ip);
                    }
                }
            }
            if (!empty($_SERVER['HTTP_X_FORWARDED']) and static::validate($_SERVER['HTTP_X_FORWARDED'])) {
                return $_SERVER['HTTP_X_FORWARDED'];
            }
            if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) and static::validate($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
                return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
            }
            if (!empty($_SERVER['HTTP_FORWARDED_FOR']) and static::validate($_SERVER['HTTP_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_FORWARDED_FOR'];
            }
            if (!empty($_SERVER['HTTP_FORWARDED']) and static::validate($_SERVER['HTTP_FORWARDED'])) {
                return $_SERVER['HTTP_FORWARDED'];
            }
        }
        
        // Default.
        return $_SERVER['REMOTE_ADDR'];
    }
    
    /**
    * Validate an IP address.
    * 
    * @param    string  $ip             IP address to test.
    * @return   bool
    */
    static public function validate(string $ip) : bool 
    {
        if (false === filter_var(
            $ip, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }
        return true;
    }  
      
    /**
    * Validate an IPV4 IP address.
    * 
    * @param    string  $ip             IP address to test.
    * @return   bool
    */
    static public function validateIpv4(string $ip) : bool 
    {
        if (false === filter_var(
            $ip, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }
        return true;
    }  
      
    /**
    * Validate an IPV4 IP address loosely.
    * 
    * @param    string  $ip             IP address to test.
    * @return   bool
    */
    static public function validateIpv4Loose(string $ip) : bool 
    {
        if (false === filter_var(
            $ip, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4)) {
            return false;
        }
        return true;
    }   
     
    /**
    * Return a netmask string if given an integer between 0 and 32. 
    * Usage:
    *     CIpAddress::CIDRtoMask(22);
    * Result:
    *     string(13) "255.255.252.0"
    * @param    int     $int    Between 0 and 32.
    * @return   string          Netmask ip address
    */
    static public function CIDRtoMask(int $int) : string 
    {
        return long2ip(-1 << (32 - (int)$int));
    }
 
    /**
    * Return the number of bits that are set in an integer.
    * Usage:
    *     CIpAddress::countSetBits(ip2long('255.255.252.0'));
    * Result:
    *     int(22)
    * @param    int     $int    A number
    * @return   int             Number of bits set.
    */
    static public function countSetbits(int $int) : int
    {
        $int = $int & 0xFFFFFFFF; // fix for extra 32 bits
        $int = ( $int & 0x55555555 ) + ( ( $int >> 1 ) & 0x55555555 );
        $int = ( $int & 0x33333333 ) + ( ( $int >> 2 ) & 0x33333333 );
        $int = ( $int & 0x0F0F0F0F ) + ( ( $int >> 4 ) & 0x0F0F0F0F );
        $int = ( $int & 0x00FF00FF ) + ( ( $int >> 8 ) & 0x00FF00FF );
        $int = ( $int & 0x0000FFFF ) + ( ( $int >>16 ) & 0x0000FFFF );
        $int = $int & 0x0000003F;
        return $int;
                
        //$int = $int - (($int >> 1) & 0x55555555);
        //$int = ($int & 0x33333333) + (($int >> 2) & 0x33333333);
        //return (($int + ($int >> 4) & 0xF0F0F0F) * 0x1010101) >> 24;
    }
    
    /**
    * Determine if a string is a valid netmask.
    * Usage:
    *     CIpAddress::validNetMask('255.255.252.0');
    *     CIpAddress::validNetMask('127.0.0.1');
    * Result:
    *     bool(true)
    *     bool(false)
    * @param    string  $netmask    An IPV4 formatted ip address.
    * @return   bool                True if a valid netmask.
    */
    static public function validNetMask(string $netmask) : bool
    {
        $netmask = ip2long($netmask);
        $neg = ((~(int)$netmask) & 0xFFFFFFFF);
        return (($neg + 1) & $neg) === 0;
    }
 
    /**
    * Return a CIDR block number when given a valid netmask.
    * Usage:
    *     CIpAddress::maskToCIDR('255.255.252.0');
    * Result:
    *     int(22)
    * @param    string  $netmask    An IPV4 formatted ip address.
    * @return   int                 CIDR number.
    * @throws   InvalidArgumentException
    */
    static public function maskToCIDR(string $netmask) : int
    {
        if (static::validNetMask($netmask)) {
            return static::countSetBits(ip2long($netmask));
        } else {
            throw new InvalidArgumentException(sprintf("Invalid netmask '%s'", $netmask));
        }
    }
 
    /**
    * It takes an ip address and a netmask and returns a valid CIDR
    * block.
    * Usage:
    *     CIpAddress::alignedCIDR('127.0.0.1','255.255.252.0');
    * Result:
    *     string(12) "127.0.0.0/22"
    * @param    string  $ipinput    An IPV4 formatted ip address.
    * @param    string  $netmask    An IPV4 formatted ip address.
    * @return   string              CIDR block.
    */
    static public function alignedCIDR(string $ipinput, string $netmask) : string
    {
        $alignedIP = long2ip((ip2long($ipinput)) & (ip2long($netmask)));
        return "$alignedIP/" . static::maskToCIDR($netmask);
    }
 
    /**
    * Check whether an IP is within a CIDR block.    
    * Usage:
    *     CIpAddress::IPisWithinCIDR('127.0.0.33','127.0.0.1/24');
    *     CIpAddress::IPisWithinCIDR('127.0.0.33','127.0.0.1/27');
    * Result: 
    *     bool(true)
    *     bool(false)
    * @param    string $ipinput     An IPv4 formatted ip address.
    * @param    string $cidr        An IPv4 formatted CIDR block. Block is aligned during execution.
    * @return   string              CIDR block.
    */
    static public function IPisWithinCIDR(string $ipinput, string $cidr)
    {
        $cidr = explode('/',$cidr);
        $cidr = static::alignedCIDR($cidr[0],static::CIDRtoMask((int)$cidr[1]));
        $cidr = explode('/',$cidr);
        $ipinput = (ip2long($ipinput));
        $ip1 = (ip2long($cidr[0]));
        $ip2 = ($ip1 + pow(2, (32 - (int)$cidr[1])) - 1);
        return (($ip1 <= $ipinput) && ($ipinput <= $ip2));
    }
 
    /**
    * Determines the largest CIDR block that an IP address will fit into.
    * Used to develop a list of CIDR blocks.
    * Usage:
    *     CIpAddress::maxBlock("127.0.0.1");
    *     CIpAddress::maxBlock("127.0.0.0");
    * Result:
    *     int(32)
    *     int(8)
    * @param    string  $ipinput    An IPv4 formatted ip address.
    * @return   int                 CIDR number.
    */
    static public function maxBlock(string $ipinput) : int 
    {
        return static::maskToCIDR(long2ip(-(ip2long($ipinput) & -(ip2long($ipinput)))));
    }
    
    /**
    * Returns an array of CIDR blocks that fit into a specified range of
    * ip addresses.
    * Usage:
    *     CIpAddress::rangeToCIDRList("127.0.0.1","127.0.0.34");
    * Result:
    *     array(7) { 
    *       [0]=> string(12) "127.0.0.1/32"
    *       [1]=> string(12) "127.0.0.2/31"
    *       [2]=> string(12) "127.0.0.4/30"
    *       [3]=> string(12) "127.0.0.8/29"
    *       [4]=> string(13) "127.0.0.16/28"
    *       [5]=> string(13) "127.0.0.32/31"
    *       [6]=> string(13) "127.0.0.34/32"
    *     }
    * @param    string  $startIPinput   An IPv4 formatted ip address.
    * @param    string  $startIPinput   An IPv4 formatted ip address.
    * @return   string[]                CIDR blocks in a numbered array.
    */
    static public function rangeToCIDRList(string $startIPinput, ?string $endIPinput = NULL) : array 
    {
        $startIPinput = trim($startIPinput);
        $endIPinput = trim($endIPinput);
        $start = ip2long(trim($startIPinput));
        $end = (empty($endIPinput)) ? $start : ip2long($endIPinput);
        while($end >= $start) {
            $maxsize = static::maxBlock(long2ip($start));
            $maxdiff = 32 - intval(log($end - $start + 1)/log(2));
            $size = ($maxsize > $maxdiff) ? $maxsize : $maxdiff;
            $listCIDRs[] = long2ip($start) . "/$size";
            $start += pow(2, (32 - $size));
        }
        return $listCIDRs;
    }
 
    /**
    * Returns an array of only two IPv4 addresses that have the lowest ip
    * address as the first entry. If you need to check to see if an IPv4
    * address is within range please use the IPisWithinCIDR method above.
    * Usage:
    *     CIpAddress::cidrToRange("127.0.0.128/25");
    * Result:
    *     array(2) {
    *       [0]=> string(11) "127.0.0.128"
    *       [1]=> string(11) "127.0.0.255"
    *     }
    * @param    string  $cidr   CIDR block
    * @return   string[]        Low end of range then high end of range.
    */
    static public function cidrToRange(string $cidr) : array 
    {
        $cnt = substr_count($cidr, '.');
        if ($cnt < 3) {
            $split = explode('/', $cidr);
            for ($i = $cnt; $i < 3; $i++) {
                $split[0] .= '.0';
            }
            $cidr = implode('/', $split);
        }
        $range = array();
        $cidr = explode('/', $cidr);
        $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
        $range[1] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        return $range;
    }
}
