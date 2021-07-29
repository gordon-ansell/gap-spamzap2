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
namespace GreenFedora\Wordpress;

/**
 * Plugin user interface.
 */
interface PluginUserInterface
{
    /**
     * Initialisation.
     * 
     * @return  void 
     */
    public function init();
    
    /**
     * Called when plugins loaded (plugins_loaded trigger in WP).
     * 
     * @return  void
     */
    public function pluginsLoaded();
}
