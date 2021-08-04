<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App;

use App\PageProcessor\AddRulePageProcessor;
use App\PageProcessor\ManageRulesPageProcessor;
use App\PageProcessor\LogPageProcessor;
use App\PageProcessor\AuthLogPageProcessor;
use App\PageProcessor\AuthCountPageProcessor;
use App\PageProcessor\LookupIpPageProcessor;
use App\PageProcessor\OptionsPageProcessor;
use GreenFedora\Wordpress\PluginAdmin;
use GreenFedora\Wordpress\PluginAdminInterface;

/**
 * Admin-side handling class.
 */
class AdminMode extends PluginAdmin implements PluginAdminInterface
{
    /**
     * Enqueue scripts.
     * 
     * @return  void
     */
    public function adminEnqueueScripts(): void
    {
       // Enqueue the style.
       //\wp_register_style($this->spref('SpamZap2Stylesheet'), \plugins_url('spamzap2\css\spamzap.css'));
        \wp_enqueue_style($this->spref('SpamZap2Stylesheet'), \plugins_url('spamzap2\assets\css\spamzap.css'), array(),
            \filemtime(ABSPATH . 'wp-content/plugins/spamzap2/assets/css/spamzap.css'));

        \wp_enqueue_script('spamzap2scripts', \plugins_url('spamzap2\assets\js\support.js'), array(),
            \filemtime(ABSPATH . 'wp-content/plugins/spamzap2/assets/js/support.js'));
        
    }

    /**
     * Side menu.
     * 
     * @return  void
     */
    public function adminMenu(): void
    {
        //$cfg = $this->app->getConfig('plugin');
        
        \add_menu_page(
            $this->cfg->title,
            $this->cfg->title,
            'manage_options',
            $this->cfg->slug,
            array($this, 'adminPageLogs'),
            'dashicons-thumbs-down'
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Logs",
            "Logs",
            'manage_options',
            $this->cfg->slug,
            array($this, 'adminPageLogs')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Options",
            "Options",
            'manage_options',
            $this->cfg->slug . "-options",
            array($this, 'adminPageOptions')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Add Rule",
            "Add Rule",
            'manage_options',
            $this->cfg->slug . "-add-rule",
            array($this, 'adminPageAddRule')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Manage Rules",
            "Manage Rules",
            'manage_options',
            $this->cfg->slug . "-manage-rules",
            array($this, 'adminPageManageRules')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Auth Logs",
            "Auth Logs",
            'manage_options',
            $this->cfg->slug . "-auth-logs",
            array($this, 'adminPageAuthLogs')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Auth Counts",
            "Auth Counts",
            'manage_options',
            $this->cfg->slug . "-auth-count",
            array($this, 'adminPageAuthCount')
        );
        \add_submenu_page(
            $this->cfg->slug,
            "SpamZap2 Lookup IP",
            "Lookup IP",
            'manage_options',
            $this->cfg->slug . "-lookup-ip",
            array($this, 'adminPageLookupIp')
        );
    }

    /**
     * Logs page.
     * 
     * @return  
     */
    public function adminPageLogs()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new LogPageProcessor($this);
        $pp->process();

    }

    /**
     * Auth logs page.
     * 
     * @return  
     */
    public function adminPageAuthLogs()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new AuthLogPageProcessor($this);
        $pp->process();

    }

    /**
     * Auth count page.
     * 
     * @return  
     */
    public function adminPageAuthCount()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new AuthCountPageProcessor($this);
        $pp->process();

    }

    /**
     * Options page.
     * 
     * @return  
     */
    public function adminPageOptions()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new OptionsPageProcessor($this);
        $pp->process();

    }

    /**
     * Add rule page.
     * 
     * @return  
     */
    public function adminPageAddRule()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new AddRulePageProcessor($this);
        $pp->process();

    }

    /**
     * Manage rules page.
     * 
     * @return  
     */
    public function adminPageManageRules()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new ManageRulesPageProcessor($this);
        $pp->process();

    }

    /**
     * Lookup IP page.
     * 
     * @return  
     */
    public function adminPageLookupIp()
    {
        // Check user is authorised.
        if (!\current_user_can('manage_options')) {
            \wp_die($this->__('You do not have sufficient permissions to access this page.'));
        }

        $pp = new LookupIpPageProcessor($this);
        $pp->process();

    }
}
