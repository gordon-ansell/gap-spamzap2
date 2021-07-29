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

use GreenFedora\Wordpress\PluginAdminInterface;
use GreenFedora\Wordpress\PluginBase;

/**
 * Plugin admin class.
 */
class PluginAdmin extends PluginBase implements PluginAdminInterface
{
    /**
     * Constructor.
     * 
     * @param    WordpressApplicationInterface  $app    Application.
     * 
     * @return   void
     */
    public function __construct(WordpressApplicationInterface $app)
    {
        parent::__construct($app);
        $this->init();
    }

    /**
     * Initialisation.
     *
     * @return  void
     */
    protected function init(): void
    {
        // Enqueue scripts and styles.
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
        // Add a new sidemenu.
        add_action('admin_menu', array($this, 'adminMenu'));
    }

    /**
     * Enqueue scripts.
     * 
     * @return  void
     */
    public function adminEnqueueScripts(): void
    {
        
    }

    /**
     * Side menu.
     * 
     * @return  void
     */
    public function adminMenu(): void
    {

    }
}
