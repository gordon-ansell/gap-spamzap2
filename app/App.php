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

use GreenFedora\Wordpress\WordpressApplication;
use GreenFedora\Application\ApplicationInterface;

use App\Domain\Db\DbAccess;
use App\Domain\Db\DbModel\DomainBlockModel;
use App\Domain\Db\DbModel\EmailBlockModel;
use App\Domain\Db\DbModel\StringBlockModel;
use App\Domain\Db\DbModel\IPBlockModel;
use App\Domain\Db\DbModel\AuthErrorModel;
use App\Domain\Db\DbModel\IPTempBlockModel;
use App\Domain\Db\DbModel\IPAllowModel;
use App\Domain\Db\DbModel\IPLookupModel;
use App\Domain\Db\DbModel\LogModel;
use App\Domain\Db\DbModel\TechLogModel;
use App\Domain\Db\DbModel\SettingsModel;
use GreenFedora\Template\Template;
use App\Template\Driver\SpamZapTemplateDriver;

/**
 * The main application entry point.
 */
class App extends WordpressApplication
{
    /**
     * Application version.
     * @param string
     */
    protected static $version = '1.0.0-dev';

    /**
     * Initialisation.
     * 
     * @return  ApplicationInterface
     */
    public function init(): ApplicationInterface
    {
        parent::init();

        $this->registerSingleton('dbaccess', DbAccess::class);
        $this->registerSingleton('domainblockmodel', DomainBlockModel::class);
        $this->registerSingleton('emailblockmodel', EmailBlockModel::class);
        $this->registerSingleton('stringblockmodel', StringBlockModel::class);
        $this->registerSingleton('ipblockmodel', IPBlockModel::class);
        $this->registerSingleton('autherrormodel', AuthErrorModel::class);
        $this->registerSingleton('iptempblockmodel', IPTempBlockModel::class);
        $this->registerSingleton('ipallowmodel', IPAllowModel::class);
        $this->registerSingleton('iplookupmodel', IPLookupModel::class);
        $this->registerSingleton('logmodel', LogModel::class);
        $this->registerSingleton('techlogmodel', TechLogModel::class);
        $this->registerSingleton('settingsmodel', SettingsModel::class);

        return $this;
    }

    /**
     * Configure the template system.
     * 
     * @return  void
     */
    public function configureTemplateSystem(): void
    {
        $config = $this->getConfig('template');     
        
        $driver = null;
        if ('plates' === $config->driver) {
            //$driver = new PlatesTemplateDriver($config);
            $driver = new SpamZapTemplateDriver($config);
        }        

        $this->registerSingleton('template', Template::class, [$driver, $config]);
    }

    /**
     * Activate the plugin.
     * 
     * @return  void
     */
    public function activate()
    {
        // Create a db instance to force creation of database if necessary.
        $this->app->get('dbaccess');
    }

    /**
     * Deactivate the plugin.
     * 
     * @return  void
     */
    public function deactivate()
    {
    }
}
