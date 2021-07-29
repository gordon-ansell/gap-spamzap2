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

use GreenFedora\Application\Application;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Logger\Logger;
use GreenFedora\Logger\Formatter\StdLogFormatter;
use GreenFedora\Logger\Writer\ConsoleLogWriter;
use GreenFedora\Logger\Writer\FileLogWriter;
use GreenFedora\Session\Session;
use GreenFedora\Wordpress\PluginUserInterface;
use GreenFedora\Wordpress\PluginAdminInterface;
use GreenFedora\Wordpress\PluginUpdateInterface;


/**
 * Wordpress application.
 */
abstract class WordpressApplication extends Application implements WordpressApplicationInterface
{
    /**
     * Plugin file.
     * @var string
     */
    protected $pluginFile = null;

    /**
     * Constructor.
     * 
     * @param   string              $pluginFile Plugin file name.
     * @param   string              $basePath   Base path to the application.
     * @param   string              $appPath    Application path.
     * @param   LoggerInterface     $logger     Logger.
     * @return  void
     */
    public function __construct(string $pluginFile, string $basePath, string $appPath, LoggerInterface $logger = null)
    {
        $this->pluginFile = $pluginFile;
        $this->setPluginFile($pluginFile);
        parent::__construct($basePath, $appPath, $logger);
    }

    /**
     * Initialisation.
     * 
     * @return  WordpressApplicationInterface
     */
    public function init(): WordpressApplicationInterface
    {
        $this->registerActivationDeactivationHooks();
        return $this;
    }

    /**
     * Register activation and deactivation hooks.
     * 
     * @return  void
     */
    public function registerActivationDeactivationHooks()
    {
        \register_activation_hook($this->getPluginFile(), array($this, 'activate'));
        \register_deactivation_hook($this->getPluginFile(), array($this, 'deactivate'));     
    }

    /**
     * Configure a standard logger.
     * 
     * @return  LoggerInterface
     */
    public function configureStandardLogger(): LoggerInterface
    {
        $config = $this->getConfig('logger');        
        $formatter = new StdLogFormatter($config);
        $writers = [
            new ConsoleLogWriter($config, $formatter), 
            new FileLogWriter($config, $formatter)
        ];
        $this->registerSingleton('logger', Logger::class, [null, $writers]);
        $logger = $this->get('logger');
        if ($this->hasConfig('env.LOG_LEVEL')) {
            $logger->level($this->getConfig('env.LOG_LEVEL'));
        }
        $this->setLogger($logger);
        return $logger;
    }

    /**
     * Activate the wordpress plugin.
     * 
     * @return  void
     */
    abstract public function activate();

    /**
     * Deactivate the wordpress plugin.
     * 
     * @return  void
     */
    abstract public function deactivate();

    /**
     * Set the plugin file.
     * 
     * @param   string  $pluginFile   Plugin file to set.
     * 
     * @return  WordpressApplicationInterface
     */
    public function setPluginFile(string $pluginFile): WordpressApplicationInterface
    {
        $this->registerValue('pluginFile', $pluginFile);
        return $this;
    }

    /**
     * Get the plugin file.
     * 
     * @return string
     */
    public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

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
        ?PluginUpdateInterface $update = null): void
    {
    }
}
