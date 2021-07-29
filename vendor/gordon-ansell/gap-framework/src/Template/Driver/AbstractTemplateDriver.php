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
namespace GreenFedora\Template\Driver;

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Template\Driver\Exception\DomainException;

/**
 * Base template driver.
 */
abstract class AbstractTemplateDriver
{
    /**
     * Configs.
     * @var ArrInterface
     */
    protected $cfg = null;

    /**
     * Constructor.
     * 
     * @param   ArrInterface                $cfg        Config.
     * 
     * @return  void
     * 
     * @throws  DomainException
     */
    public function __construct(ArrInterface $cfg = null)
    {
        $this->cfg = $cfg;

        if (!$this->cfg->has('path')) {
            throw new DomainException("No template path defined in the configs.");
        }
    }

    /**
     * Load extensions.
     * 
     * @return
     */
    protected function loadExtensions()
    {

    }
}
