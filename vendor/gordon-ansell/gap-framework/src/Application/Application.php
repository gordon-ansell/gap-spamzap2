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
namespace GreenFedora\Application;

use GreenFedora\Application\ApplicationInterface;
use GreenFedora\Container\Container;
use GreenFedora\Stdlib\Path;
use GreenFedora\Application\Exception\RuntimeException;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\Env\Env;
use GreenFedora\Config\Config;

/**
 * The main application entry point.
 */
class Application extends Container implements ApplicationInterface
{
    /**
     * Framework version.
     */
    const FRAMEWORK_VERSION = '1.0.0-dev';

    /**
     * Application version.
     * @param string
     */
    protected static $version = null;

    /**
     * The base path.
     * @var string|null
     */
    protected $basePath = null;

    /**
     * Application path.
     * @var string|null
     */
    protected $appPath = null;

    /**
     * The root namespace.
     * @var string|null
     */
    protected $namespace = null;

    /**
     * Constructor.
     * 
     * @param   string              $basePath   Base path to the application.
     * @param   string              $appPath    Application path.
     * @param   LoggerInterface     $logger     Logger.
     * @return  void
     */
    public function __construct(string $basePath, string $appPath, LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->setBasePath($basePath);
        $this->setAppPath($appPath);
        if (!is_null($logger)) {
            $this->setLogger($logger);
        }

        if (is_null(static::$version)) {
            throw new RuntimeException("Set the 'version' class variable in the application.");
        }

        $this->registerValue('version', static::$version);
        $this->init();
    }

    /**
     * Initialisation.
     * 
     * @return  ApplicationInterface
     */
    public function init(): ApplicationInterface
    {
        return $this;
    }

    /**
     * Load the configs (and environment variables).
     * 
     * @param   string  $basePath       The base path.
     * @return  void
     */
    public function loadConfigs(string $basePath): void
    {
        // Get the environment variables.
        $env = (new Env($basePath))->addVar('BASEPATH', $basePath);

        // What mode are we in?
        $mode = $env->get('APPLICATION_ENV');

        // Load the configs.
        $config = (new Config())->setBasePath($basePath)->process($mode);
        $config->setDotted('env', $env->getData());
        $this->setConfig($config);
    }

    /**
     * Configure a standard logger.
     * 
     * @return  LoggerInterface
     */
    public function configureStandardLogger(): LoggerInterface
    {
        throw new RuntimeException("Apllication's configureStandardLogger needs to be overloaded.");
    }

    /**
     * Set the base path.
     * 
     * @param   string  $basePath   Base path to set.
     * @return  ApplicationInterface
     */
    public function setBasePath(string $basePath): ApplicationInterface
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->registerValue('basePath', $this->basePath);
        return $this;
    }

    /**
     * Set the app path.
     * 
     * @param   string  $appPath    App path to set.
     * @return  ApplicationInterface
     */
    public function setAppPath(string $appPath): ApplicationInterface
    {
        $this->appPath = rtrim($appPath, '\/');
        $this->registerValue('appPath', $this->appPath);
        return $this;
    }

    /**
     * Get the application path.
     * 
     * @return  string
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * Get the application namespace.
     *
     * @return string
     * @throws RuntimeException
     */
    public function getNamespace()
    {
        if (!is_null($this->namespace)) {
            return $this->namespace;
        }

        $here = trim(str_replace($this->basePath, '', $this->appPath), '\/') . '/';

        $composer = json_decode(file_get_contents(Path::join($this->basePath, 'composer.json'), true));

        $psr4 = $composer->autoload->{'psr-4'};

        foreach($psr4 as $k => $v) {
            if ($v === $here) {
                $this->registerValue('application_namespace', rtrim($k, '\\'));
                return $this->namespace = rtrim($k, '\\');
            }
        }

        throw new RuntimeException('Cannot detect application namespace in composer.json.');
    }
}
