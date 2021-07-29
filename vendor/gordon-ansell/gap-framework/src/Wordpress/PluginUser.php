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

use GreenFedora\Wordpress\WordpressApplicationInterface;
use GreenFedora\Wordpress\PluginBase;

/**
 * Plugin user class.
 */
abstract class PluginUser extends PluginBase
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
    public function init()
    {
        \add_action('plugins_loaded', array($this, 'pluginsLoaded'));   
    }

    /**
     * Called when plugins loaded (plugins_loaded trigger in WP).
     * 
     * @return  void
     */
    abstract public function pluginsLoaded();
}
