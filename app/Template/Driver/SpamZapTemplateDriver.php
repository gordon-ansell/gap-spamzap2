<?php
/**
 * This file is part of the SpamZap2 framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Template\Driver;

use GreenFedora\Template\Driver\PlatesTemplateDriver;
use App\Template\Extension\IPLookupExtension;

/**
 * Template driver for SpamZap2.
 */
class SpamZapTemplateDriver extends PlatesTemplateDriver
{
    /**
     * Load extensions.
     * 
     * @return
     */
    protected function loadExtensions()
    {
        $this->engine->loadExtension(new IPLookupExtension());
        $this->engine->loadExtension(new \League\Plates\Extension\URI('/'));
    }
}
