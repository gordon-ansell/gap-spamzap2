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

use GreenFedora\Container\ContainerInterface;
use GreenFedora\Logger\LoggerInterface;

/**
 * Interface for the Application class.
 */
interface ApplicationInterface extends ContainerInterface
{
    /**
     * Initialisation.
     * 
     * @return  ApplicationInterface
     */
    public function init(): ApplicationInterface;

    /**
     * Load the configs (and environment variables).
     * 
     * @param   string  $basePath       The base path.
     * @return  void
     */
    public function loadConfigs(string $basePath): void;

    /**
     * Configure a standard logger.
     * 
     * @return  LoggerInterface
     */
    public function configureStandardLogger(): LoggerInterface;

    /**
     * Set the base path.
     * 
     * @param   string  $basePath   Base path to set.
     * @return  ApplicationInterface
     */
    public function setBasePath(string $basePath): ApplicationInterface;

    /**
     * Set the app path.
     * 
     * @param   string  $appPath    App path to set.
     * @return  ApplicationInterface
     */
    public function setAppPath(string $appPath): ApplicationInterface;

    /**
     * Get the application path.
     * 
     * @return  string
     */
    public function getAppPath(): string;

    /**
     * Get the application namespace.
     *
     * @return string
     * @throws RuntimeException
     */
    public function getNamespace();
}
