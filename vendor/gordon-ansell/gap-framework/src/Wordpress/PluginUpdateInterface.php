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
 * Plugin update interface.
 */
interface PluginUpdateInterface
{
    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param   object  $transient
     * @return  object 
     */
    public function checkUpdate($transient);
 
    /**
     * Add our self-hosted description to the filter
     *
     * @param   boolean $false
     * @param   string  $action
     * @param   object  $response
     * @return  bool|object
     */
    public function checkInfo(bool $false, $action, $response);

    /**
     * Post-install/upgrade.
     * 
     * @return
     */
    public function postInstall($true, $hook_extra, $result);
}
