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
namespace GreenFedora\Db\Platform;

/**
 * Database platform interface.
 */
interface PlatformInterface
{
    /**
     * Column properties.
     */
    const CP_LEN                =    1;
    const CP_LEN_COMP           =    2;
    const CP_DEC                =    4;
    const CP_DEC_COMP_IF_LEN    =    8;
    const CP_UNSIGNED           =   16;         // And ZEROFILL.
    const CP_FSP                =   32;
    const CP_BINARY             =   64;
    const CP_CHARSET            =  128;         // And COLLATE.
    const CP_VALS               =  256;

    /**
     * Quote an ID.
     *
     * @param   string      $raw        Raw string.
     * @return  string
     */
    public function idq(string $raw) : string;

    /**
     * Get a prefixed table name.
     *
     * @param   string      $raw        Raw table name.
     * @param   bool        $quote      Quote it?
     * @return  string
     */
    public function tn(string $raw, bool $quote = true) : string;

    /**
     * Get the column types.
     *
     * @return  array
     */
    public function getColTypes() : array;

    /**
     * Get a particular column type.
     *
     * @param   string      $type       Type to get.
     * @return  array|null
     */
    public function getColType(string $type) : ?array;

    /**
     * Get the auto increment.
     * 
     * @return  string
     */
    public function autoIncrement(): ?string;

    /**
     * Get the primary.
     * 
     * @return  string
     */
    public function primary(): ?string;

    /**
     * Get the unique.
     * 
     * @return  string
     */
    public function unique(): ?string;

    /**
     * Get the unsigned.
     * 
     * @return  string
     */
    public function unsigned(): ?string;

    /**
     * Get the primary key type.
     * 
     * @return  string
     */
    public function primaryKeyType(): ?string;

    /**
     * Allow null in primary key.
     * 
     * @return  bool
     */
    public function pkAllowNull(): ?bool;
}
