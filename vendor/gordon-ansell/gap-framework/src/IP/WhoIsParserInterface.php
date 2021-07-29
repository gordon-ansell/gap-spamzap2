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

/**
 * WhoIs Parser.
 */
interface WhoIsParserInterface
{
    /**
     * Get the owner organization.
     * 
     * @return  string
     */
    public function getOwnerOrganization(): string;

    /**
     * Get the owner name.
     * 
     * @return  string
     */
    public function getOwnerName(): string;

    /**
     * Get the owner handle.
     * 
     * @return  string
     */
    public function getOwnerHandle(): string;

    /**
     * Get the owner address.
     * 
     * @return  string|array
     */
    public function getOwnerAddress($asArray = true); 

    /**
     * Get the owner country.
     * 
     * @return  string
     */
    public function getOwnerCountry();

    /**
     * =====================================
     * ADMIN
     * =====================================
     */

    /**
     * Get the admin name.
     * 
     * @return  string
     */
    public function getAdminName() : string;

    /**
     * Get the admin role.
     * 
     * @return  string
     */
    public function getAdminRole() : string;

    /**
     * Get the admin address.
     * 
     * @return  string|array
     */
    public function getAdminAddress($asArray = true); 

    /**
     * Get the admin address line 1.
     * 
     * @return  string
     */
    public function getAdminAddressLine1(); 

    /**
     * =====================================
     * NETWORK
     * =====================================
     */

    /**
     * Get the network name.
     * 
     * @return  string
     */
    public function getNetworkName() : string;

    /**
     * Get the network status.
     * 
     * @return  string
     */
    public function getNetworkStatus() : string;

    /**
     * Get the network country.
     * 
     * @return  string
     */
    public function getNetworkCountry() : string;

    /**
     * Get the network inetnum.
     * 
     * @return  string
     */
    public function getNetworkInetnum() : string;

    /**
     * Get the network mnt-by.
     * 
     * @return  string
     */
    public function getNetworkMntBy() : string;

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
    public function appendNew(string &$existing, string $new);

    /**
     * Get the domain.
     * 
     * @return  string
     */
    public function getDomain() : string;

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName() : string;

    /**
     * Get the network name.
     * 
     * @return  string
     */
    public function getNetwork() : string;

    /**
     * Get the country.
     * 
     * @return  string
     */
    public function getCountry() : string;

    /**
     * Get the range.
     * 
     * @return  array
     */
    public function getRange() : array;

    /**
     * Get the address.
     * 
     * @return  string
     */
    public function getAddress() : string;
}
