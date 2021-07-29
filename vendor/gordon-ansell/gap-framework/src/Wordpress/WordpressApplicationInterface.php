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

use GreenFedora\Application\ApplicationInterface;
use GreenFedora\Wordpress\PluginUserInterface;
use GreenFedora\Wordpress\PluginAdminInterface;

/**
 * Interface for the Wordpress application.
 */
interface WordpressApplicationInterface extends ApplicationInterface
{
    /**
     * Initialisation.
     * 
     * @return  WordpressApplicationInterface
     */
    public function init(): WordpressApplicationInterface;

    /**
     * Get the plugin file.
     * 
     * @return string
     */
    public function getPluginFile(): string;
    
    /**
     * Run stuff.
     * 
     * @param   PluginUserInterface     $user       User-side stuff.
     * @param   PluginAdminInterface    $admin      Admin-side stuff.
     * @param   PluginUpdateInterface   $update     Update stuff.
     * 
     * @return  void     
     */
    public function run(PluginUserInterface $user, ?PluginAdminInterface $admin = null,
        ?PluginUpdateInterface $update = null): void;
}
