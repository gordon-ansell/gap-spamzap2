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

use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * Database platform.
 */
abstract class AbstractPlatform
{
    /**
     * Configs.
     * @var Arr
     */
    protected $config = null;

    /**
     * ID quote character.
     * @var string
     */
    protected $idqChar = '';

    /**
     * Column types.
     * @var array
     */
    protected $colTypes = array();

    /**
     * Column modifiers.
     * @var array
     */
    protected $colModes = array();

    /**
     * Constructor.
     *
     * @param   ArrInterface         $config     Configs.
     * @return  void
     */
    public function __construct(ArrInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Quote an ID.
     *
     * @param   string      $raw        Raw string.
     * @return  string
     */
    public function idq(string $raw) : string
    {
        if (substr($raw, 0, 1) == $this->idqChar) {
            return $raw;
        }

        if (false === strpos($raw, '.')) {
            return $this->idqChar . $raw . $this->idqChar;
        } else {
            $split = explode('.', $raw);
            $split[0] = $this->idqChar . $this->tn($split[0], false) . $this->idqChar;
            if ('*' != $split[1]) {
                $split[1] = $this->idqChar . $split[1] . $this->idqChar;
            }
            return implode('.', $split);
        }
    }

    /**
     * Get a prefixed table name.
     *
     * @param   string      $raw        Raw table name.
     * @param   bool        $quote      Quote it?
     * @return  string
     */
    public function tn(string $raw, bool $quote = true) : string
    {
        $pref = $this->config->get('pref', '');
        if ($quote and (substr($raw, 0, 1) != $this->idqChar)) {
            return $this->idqChar . $pref . $raw . $this->idqChar;
        } else {
            return $pref . $raw;
        }
    }

    /**
     * Get the column types.
     *
     * @return  array
     */
    public function getColTypes() : array
    {
        return $this->colTypes;
    }

    /**
     * Get a particular column type.
     *
     * @param   string      $type       Type to get.
     * @return  array|null
     */
    public function getColType(string $type) : ?array
    {
        if (array_key_exists(strtoupper($type), $this->colTypes)) {
            return $this->colTypes[strtoupper($type)];
        }
        return null;
    }

    /**
     * Get the auto increment.
     * 
     * @return  string
     */
    public function autoIncrement(): ?string
    {
        return $this->colMods['autoincrement'];
    }

    /**
     * Get the primary.
     * 
     * @return  string
     */
    public function primary(): ?string
    {
        return $this->colMods['primary'];
    }

    /**
     * Get the unique.
     * 
     * @return  string
     */
    public function unique(): ?string
    {
        return $this->colMods['unique'];
    }

    /**
     * Get the unsigned.
     * 
     * @return  string
     */
    public function unsigned(): ?string
    {
        return $this->colMods['unsigned'];
    }

    /**
     * Get the primary key type.
     * 
     * @return  string
     */
    public function primaryKeyType(): ?string
    {
        return $this->colMods['primarykeytype'];
    }

    /**
     * Allow null in primary key.
     * 
     * @return  bool
     */
    public function pkAllowNull(): ?bool
    {
        return $this->colMods['pkallownull'];
    }
}
