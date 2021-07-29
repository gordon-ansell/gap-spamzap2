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
namespace GreenFedora\Container;

use GreenFedora\Stdlib\Arr\DottedArrInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface for the Container class.
 * 
 * PSR-11.
 * 
 * @link https://www.php-fig.org/psr/psr-11/
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Set the configs.
     * 
     * @param   DotterArrInterface   $cfg    Cfg to set.
     * @return  ContainerInterface
     */
    public function setConfig(DottedArrInterface $cfg): ContainerInterface;

    /**
     * Do we have a config value?
     * 
     * @param   string      $key    Key to check.
     * @return  bool  
     */
    public function hasConfig(string $key): bool;

    /**
     * Get the configs.
     * 
     * @param   string|null      $key   Optional key. 
     * @return  mixed
     */
    public function getConfig(?string $key = null);

    /**
     * Register a class with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $class      A class name or an instance.
     * @param   bool        $singleton  Singleton?
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerClass(string $id, $class, bool $singleton = false, array $args = []) : ContainerInterface;

    /**
     * Register a singleton class with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $class      A class name or an instance.
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerSingleton(string $id, $class, array $args = []) : ContainerInterface;

    /**
     * Register a singleton instance with the container. This is a pre-existing instance.
     * 
     * @param   string      $id         ID.
     * @param   object      $instance   A class name or an instance.
     * @return  ContainerInterface
     */
    public function registerSingletonInstance(string $id, $instance) : ContainerInterface;

    /**
     * Register a callable with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $callable   The callable thing.
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerCallable(string $id, callable $callable, array $args = []) : ContainerInterface;

    /**
     * Register a value with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $value      The value.
     * @param   ...         $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerValue(string $id, $value) : ContainerInterface;

    /**
     * Register anything with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $thing      A class name or an instance.
     * @param   bool|null   $singleton  Singleton?
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function register(string $id, $thing, bool $singleton = false, array $args = []) : ContainerInterface;

    /**
     * Bind a class.
     * 
     * @param   string  $id     Entry ID.
     * @param   array   $args   Arguments.
     * @return  object
     * @throws  NotFoundException
     * @throws  BindErrorException
     * @throws  AlreadyExistsException
     */
    public function bind(string $id, array $args = []);

    /**
     * See if we have an entry in the container.
     * 
     * @param   string      $id         ID of entry.
     * @return  bool    
     */
    public function has(string $id): bool;

    /**
     * Get an entry from the container.
     * 
     * @param   string      $id         ID of entry.
     * @param   array       $args       Arguments.
     * @return  mixed
     */
    public function get(string $id, array $args = []);

    /**
     * Quicker access to a singleton.
     * 
     * @param   string      $id         ID of entry.
     * @return  object
     */
    public function singleton(string $id);

    /**
     * Magic getter.
     * 
     * @param   string      $id         ID to get.
     * @return  mixed
     */
    public function __get(string $id);

    /**
     * Dump the container.
     * 
     * @return  array
     */
    public function dump(): array;
}
