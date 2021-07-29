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

use GreenFedora\Wordpress\PluginUpdateInterface;
use GreenFedora\Wordpress\WordpressApplicationInterface;
use GreenFedora\Wordpress\PluginBase;

/**
 * Plugin update class.
 */
class PluginUpdate extends PluginBase implements PluginUpdateInterface
{
    /**
     * The plugin current version
     * @var string
     */
    protected $currentVersion;
 
    /**
     * The plugin remote update path
     * @var string
     */
    protected $updatePath;
 
    /**
     * Plugin Slug.
     * @var string
     */
    protected $pluginSlug;

    /**
     * Access token.
     * @var string
     */
    protected $accessToken;

    /**
     * GitHub API result.
     * @var object
     */
    protected $githubApiResult = null;

    /**
     * Plugin data.
     * @var array
     */
    protected $pluginData;
 
    /**
     * Initialize a new instance of the WordPress Auto-Update class
     *
     * @param   WordpressApplicationInterface     $app    Application.
     * @return  PluginUpdateInterface
     */
    public function __construct(WordpressApplicationInterface $app)
    {
        parent::__construct($app);

        $this->currentVersion = $this->cfg->version; 
        $this->updatePath = $this->cfg->updatepath;
        $this->pluginSlug = $this->cfg->slug . '/' . $this->cfg->slug . '.php';

        $settings = $this->getApp()->get('dbaccess')->getSettings(true);
        $this->accessToken = $settings['github-token'];
 
        // Define the alternative API for updating checking
        \add_filter('pre_set_site_transient_update_plugins', array(&$this, 'checkUpdate'));

        // Define the alternative response for information checking
        \add_filter('plugins_api', array(&$this, 'checkInfo'), 10, 3);
 
        // Define the post-install actions.
        \add_filter('upgrader_post_install', array(&$this, 'postInstall'), 10, 3);
    }

    /**
     * Set the plugin data.
     * 
     * @return  void
     */
    protected function initPluginData()
    {
        $this->pluginData = \get_plugin_data($this->app->get('pluginFile'));
    }

    /**
     * Load the repository release info.
     * 
     * @return  void
     */
    protected function loadRepoReleaseInfo()
    {
        // If we already have our transient, get out of here.
        if (!empty($this->githubAPIResult)) {
            return;
        }

        // Set up the URL.
        $url = $this->updatePath;

        // We need the access token for private repos.
        $args = [];
        if (!empty($this->accessToken)) {
            //$args['headers'] = ['Authorization' => $this->accessToken];
            $url = add_query_arg(array("access_token" => $this->accessToken), $url);
        }

        // Check github.
        $response = \wp_remote_get($url, $args);
        if (\is_wp_error($response)) {
            throw new \Exception(sprintf("Remote get for %s failed with: %s", $url, $response->get_error_message()));
        }
        $body = \wp_remote_retrieve_body($response);

        if (!empty($body)) {
            $this->githubAPIResult = @json_decode($body);

            // Keep only the latest.
            if (is_array($this->githubAPIResult)) {
                $this->githubAPIResult = $this->githubAPIResult[0];
            }   
        } 
 
    }
 
    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param   object  $transient
     * @return  object 
     */
    public function checkUpdate($transient)
    {
        // If we've already checked, don't do it again.
        if (empty($transient->checked)) {
            return $transient;
        }

        // Initialise plugin data.
        $this->initPluginData();

        // Load the release info.
        $this->loadRepoReleaseInfo();

        // Compare the versions.
        $doUpdate = version_compare($this->githubAPIResult->tag_name, $transient->checked[$this->pluginSlug]);


        // Update the transient to include our updated plugin data
        if (1 == $doUpdate) {
            $package = $this->githubAPIResult->zipball_url;
        
            // Include the access token for private GitHub repos.
            $args = [];
            if (!empty( $this->accessToken)) {
                //$args['headers'] = ['Authorization' => $this->accessToken];
                $package = \add_query_arg(array("access_token" => $this->accessToken), $package);
            }
        
            $obj = new \stdClass();
            $obj->slug = $this->pluginSlug;
            $obj->new_version = $this->githubAPIResult->tag_name;
            $obj->url = $this->pluginData["PluginURI"];
            $obj->package = $package;
            $obj->args = $args;
            $transient->response[$this->pluginSlug] = $obj;
        }

        return $transient;
    }
 
    /**
     * Add our self-hosted description to the filter
     *
     * @param   boolean $false
     * @param   string  $action
     * @param   object  $response
     * @return  bool|object
     */
    public function checkInfo(bool $false, $action, $response)
    {
        // If nothing is found, do nothing
        if (empty($response->slug) || $response->slug != $this->pluginSlug ) {
            return false;
        }

        // Initialise the plugin data.
        $this->initPluginData();

        // Load the release info.
        $this->loadRepoReleaseInfo();

        // Add our plugin information.
        $response->last_updated = $this->githubAPIResult->published_at;
        $response->slug = $this->pluginSlug;
        $response->name  = $this->pluginData["Name"];
        $response->plugin_name  = $this->pluginData["Name"];
        $response->version = $this->githubAPIResult->tag_name;
        $response->author = $this->pluginData["AuthorName"];
        $response->homepage = $this->pluginData["PluginURI"];
        $response->sections = [
            'description' => $this->pluginData['Description'],
            //'changelog' => $this->githubApiResult['body'],

        ];

        // This is our release download zip file.
        $downloadLink = $this->githubAPIResult->zipball_url;

        // Include the access token for private GitHub repos
        if (!empty($this->accessToken)) {
            $downloadLink = \add_query_arg(
                array("access_token" => $this->accessToken),
                $downloadLink
            );
        }
        $response->download_link = $downloadLink;

        // Gets the required version of WP if available
        $matches = null;
        preg_match("/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->requires = $matches[1];
                }
            }
        }
        
        // Gets the tested version of WP if available
        $matches = null;
        preg_match("/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
        if (!empty($matches)) {
            if (is_array($matches)) {
                if (count($matches) > 1) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }
 

    /**
     * Post-install/upgrade.
     * 
     * @return
     */
    public function postInstall($true, $hook_extra, $result)
    {
        return $result;
    }

}
