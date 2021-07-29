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

use GreenFedora\Attribute\AttributeClass;
use GreenFedora\Container\ContainerInterface;
use GreenFedora\Container\Exception\AlreadyExistsException;
use GreenFedora\Container\Exception\BindErrorException;
use GreenFedora\Container\Exception\InvalidAttributeException;
use GreenFedora\Container\Exception\NotFoundException;
use GreenFedora\Container\Exception\ResolveErrorException;
use GreenFedora\Container\Exception\UnexpectedValueException;
use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\Arr\DottedArr;
use GreenFedora\Stdlib\Arr\DottedArrInterface;

/**
 * Dependency injection container.
 * 
 * PSR-11.
 * 
 * @link https://www.php-fig.org/psr/psr-11/
 */
class Container implements ContainerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Global container.
     * @var ContainerInterface
     */
    protected static $instance = null;

    /**
     * The map of class entries in the container.
     * 
     * id => [class, singleton, args] 
     * 
     * @var array
     */
    protected $classMap = [];

    /**
     * The reverse map of class entries in the container.
     * 
     * class => id 
     * 
     * @var array
     */
    protected $reverseClassMap = [];

    /**
     * The map of callable entries in the container.
     * 
     * id => [callable, args]
     * 
     * @var array
     */
    protected $callableMap = [];

    /**
     * The map of value entries in the container.
     * 
     * id => value
     * 
     * @var array
     */
    protected $valueMap = [];

    /**
     * The map of instances in the container.
     * 
     * id => instance
     * 
     * @var object[]
     */
    protected $instances = [];

    /**
     * Config.
     * 
     * @var DottedArrInterface
     */
    protected $cfg = null;

    /**
     * Constructor.
     * 
     * @param   LoggerInterface     $logger         Logger.
     * @return  void
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
        $this->cfg = new DottedArr();
    }

    /**
     * The main register function.
     * 
     * @param   string      $type       Type to register.
     * @param   string      $id         ID.
     * @param   mixed       $thing      A class name or an instance.
     * @param   bool|null   $singleton  Singleton?
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     * @throws  AlreadyExistsException
     * @throws  UnexpectedValueException
     */
    protected function registerByType(string $type, string $id, $thing, bool $singleton = false, 
    array $args = []) : ContainerInterface
    {
        if ($this->has($id)) {
            throw new AlreadyExistsException(sprintf("The dependency injection container already has an entry with ID '%s'.",
                $id));
        }

        if ('class' === $type) {
            if (array_key_exists($thing, $this->reverseClassMap)) {
                throw new AlreadyExistsException(sprintf(
                    "The dependency injection container already has an entry for class '%s', it has ID: '%s'.",
                    $thing, $this->reverseClassMap[$thing]));
            }
            $this->classMap[$id] = ['class' => $thing, 'singleton' => $singleton, 'args' => $args];
            $this->reverseClassMap[$thing] = $id;
        } else if ('callable' === $type) {
            $this->callableMap[$id] = ['callable' => $thing, 'args' => $args];
        } else if ('value' === $type) {
            $this->valueMap[$id] = $thing;
        } else {
            throw new UnexpectedValueException(sprintf("Cannot register ID '%s', '%s' is an invalid type of map entry.",
                $id, $type));
        }

        return $this;
    }

    /**
     * Set the configs.
     * 
     * @param   DotterArrInterface   $cfg    Cfg to set.
     * @return  ContainerInterface
     */
    public function setConfig(DottedArrInterface $cfg): ContainerInterface
    {
        $this->cfg = $cfg;
        return $this;
    }

    /**
     * Do we have a config value?
     * 
     * @param   string      $key    Key to check.
     * @return  bool  
     */
    public function hasConfig(string $key): bool
    {
        return $this->cfg->hasDotted($key);
    }

    /**
     * Get the configs.
     * 
     * @param   string|null      $key   Optional key. 
     * @return  mixed
     */
    public function getConfig(?string $key = null)
    {
        if (is_null($key)) {
            return $this->cfg;
        }
        return $this->cfg->dotted($key, null);
    }

    /**
     * Register a class with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $class      A class name or an instance.
     * @param   bool        $singleton  Singleton?
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerClass(string $id, $class, bool $singleton = false, array $args = []) : ContainerInterface
    {
        return $this->registerByType('class', $id, $class, $singleton, $args);
    }

    /**
     * Register a singleton class with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $class      A class name or an instance.
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerSingleton(string $id, $class, array $args = []) : ContainerInterface
    {
        return $this->registerByType('class', $id, $class, true, $args);
    }

    /**
     * Register a singleton instance with the container. This is a pre-existing instance.
     * 
     * @param   string      $id         ID.
     * @param   object      $instance   A class name or an instance.
     * @return  ContainerInterface
     */
    public function registerSingletonInstance(string $id, $instance) : ContainerInterface
    {
        $this->instances[$id] = $instance;
        $this->reverseClassMap[get_class($instance)] = $id;
        return $this;
    }

    /**
     * Register a callable with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $callable   The callable thing.
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerCallable(string $id, callable $callable, array $args = []) : ContainerInterface
    {
        return $this->registerByType('callable', $id, $callable, false, $args);
    }

    /**
     * Register a value with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $value      The value.
     * @param   ...         $args       Arguments.
     * @return  ContainerInterface
     */
    public function registerValue(string $id, $value) : ContainerInterface
    {
        return $this->registerByType('value', $id, $value);
    }

    /**
     * Register anything with the container.
     * 
     * @param   string      $id         ID.
     * @param   mixed       $thing      A class name or an instance.
     * @param   bool|null   $singleton  Singleton?
     * @param   array       $args       Arguments.
     * @return  ContainerInterface
     */
    public function register(string $id, $thing, bool $singleton = false, array $args = []) : ContainerInterface
    {
        $isClass = true;

        try {
            new \ReflectionClass($thing);
        } catch (\Exception $ex) {
            $isClass = false;
        }

        if ($isClass) {
            return $this->registerClass($id, $thing, $singleton, $args);
        } else if (is_callable($thing)) {
            return $this->registerCallable($id, $thing, $args);
        } else {
            return $this->registerValue($id, $thing);
        }

    }

    /**
     * Bind attribute injections.
     * 
     * 
     * @param   \ReflectionClass    $reflectionClass    Class to bind to.
     * @param   array               $rawArgs            Raw arguments.
     * @return  array                                   Bound arguments. 
     * @throws  InvalidAttributeException.
     */
    protected function bindAttributeInjections(\ReflectionClass $reflectionClass, array $rawArgs = [])
    {
        // Create an attribute class.
        $rcn = $reflectionClass->getName();
        $attrClass = new AttributeClass($rcn);
        $attributes = $attrClass->getAttributes();

        if (!is_null($this->logger)) {
            $this->debug(sprintf("Binding attribute injections for %s.", $rcn), null, __METHOD__);
        }

        if (!is_null($this->logger)) {
            $this->debug(sprintf("%s injectable class attributes found for %s.", count($attributes), $rcn), null, __METHOD__);
        }

        // If no class attributes ...
        if (0 === count($attributes)) {
            // ... see if there are constructor attributes.
            $constructor = $attrClass->getConstructor();
            if (!is_null($constructor)) {
                $attributes = $constructor->getAttributes();
                if (!is_null($this->logger)) {
                    $this->debug(sprintf("%s injectable constructor attributes found for %s.", 
                        count($attributes), $rcn), null, __METHOD__);
                }
                if (0 === count($attributes)) {
                    return $rawArgs;
                }
            } else {
                return $rawArgs;
            }
        }

        // If we have no contructor, just quit..
        if (is_null($reflectionClass->getConstructor())) {
            return $rawArgs;
        }

        // Possible injections.
        $possibles = [];

        // Parse all the attributes into name => value pairs.
        foreach ($attributes as $attribute) {
            // Only interested in injections.
            if ('Inject' != $attribute->getName()) {
                continue;
            }

            // Get the arguments.
            $attrArgs = $attribute->getArguments();

            if (!is_null($this->logger)) {
                $this->debug(sprintf("We have %s potential attribute injections for %s.", 
                    count($attrArgs), $rcn), null, __METHOD__);
            }

            // If we have some arguments ...
            if (0 !== count($attrArgs)) {

                if (!is_null($this->logger)) {
                    $this->debug(sprintf("%s injectable arguments found for %s.", count($attrArgs), $rcn), null, __METHOD__);
                }

                foreach ($attrArgs as $arg) {
                    // Sanity check on the attribute.
                    if (false === strpos($arg, ':') or substr_count($arg, ':') > 1) {
                        throw new InvalidAttributeException(
                            sprintf("Invalid inject attribute. Inject attributes must contain a single ':'. Handling class '%s'.",
                                $rcn)
                        );
                    } 

                    // Okay, it's valid, let's extract it.
                    $parts = explode(':', $arg);

                    // Save it.
                    $possibles[$parts[0]] = $parts[1];         

                    if (!is_null($this->logger)) {
                        $this->debug(sprintf("Setting injectable argument index '%s' = '%s' found for %s.", 
                            $parts[0], $parts[1], $rcn), null, __METHOD__);
                    }
                }
            } else {
                if (!is_null($this->logger)) {
                    $this->debug(sprintf("0 injectable arguments found for %s.", $rcn), null, __METHOD__);
                }
            }
        }

        // If we have no possibles, quit.
        if (0 === count($possibles)) {
            if (!is_null($this->logger)) {
                $this->debug(sprintf("0 possible injections found for %s.", $rcn), null, __METHOD__);
            }
            return $rawArgs;
        } else {
            if (!is_null($this->logger)) {
                $this->debug(sprintf("%s possible injections found for %s.", count($possibles), $rcn), null, __METHOD__);
            }
        }

        // Loop through constructor parameters to find matches.
        $count = 0;
        $injected = 0;
        $cparams = $reflectionClass->getConstructor()->getParameters();

        if (!is_null($this->logger)) {
            $this->debug(sprintf("Constructor reflector shows %s parameters for %s.", count($cparams), $rcn), null, __METHOD__);
        }

        foreach ($cparams as $p) {
            
            if (array_key_exists($p->getName(), $possibles)) {

                if (!is_null($this->logger)) {
                    $this->debug(sprintf("'%s' is a possible injection because it's valid for the constructor, found for %s.", 
                        $p->getName(), $rcn), null, __METHOD__);
                }

                if ($count >= count($rawArgs) or is_null($rawArgs[$count])) {

                    $thisVal = $possibles[$p->getName()];

                    if (false !== strpos($thisVal, '|')) {

                        $parts = explode('|', trim($thisVal));

                        if ('cfg' !== trim($parts[0])) {
                            throw new InvalidAttributeException(
                                sprintf("Invalid inject attribute. '%s' is an invalid argument qualifier. Handling class '%s'.",
                                    trim($parts[0]), $rcn)
                            );
                        }

                        if (!is_null($this->logger)) {
                            $this->debug(sprintf("'%s' is a config injection, found for %s.", 
                                $p->getName(), $rcn), null, __METHOD__);
                        }

                        $cval = trim($parts[1]);
                        $rawArgs[$count] = $this->getConfig($cval);

                        $injected++;

                        if (!is_null($this->logger)) {
                            $this->debug(sprintf("Injected '%s' for argument '%s' at position %s as a config injection, found for %s.", 
                                $cval, $p->getName(), $count, $rcn), null, __METHOD__);
                        }

                    } else {

                        if (!$this->has(trim($thisVal))) {
                            throw new NotFoundException(
                                sprintf("Container entry '%s' not found for inject attribute. Handling class '%s'.",
                                    trim($thisVal), $rcn)
                            );                            
                        }

                        if (!is_null($this->logger)) {
                            $this->debug(sprintf("'%s' is a non-config injection, found for %s.", 
                                $p->getName(), $rcn), null, __METHOD__);
                        }

                        $cval = trim($thisVal);
                        $inject = $this->get($cval);
                        $rawArgs[$count] = $inject;

                        $injected++;

                        if (!is_null($this->logger)) {
                            $this->debug(sprintf("Injected '%s', which is of type %s, for argument '%s' at position %s as a config injection, found for %s.", 
                                $cval, gettype($inject), $p->getName(), $count, $rcn), null, __METHOD__);
                        }
                    }

                } else {

                    if (!is_null($this->logger)) {
                        $this->debug(sprintf("DID NOT inject '%s' because the raw argument is not null and %s < %s, found for %s.", 
                            $p->getName(), $count, count($rawArgs), $rcn), null, __METHOD__);
                    }

                }
            } else {
                if (!is_null($this->logger)) {
                    $this->debug(sprintf("Argument '%s' does not exist in the possibles array, for class %s.", 
                        $p->getName(), $rcn), null, __METHOD__);
                }
            }

            $count++;
        }

        if (!is_null($this->logger)) {
            $this->debug(sprintf("Injected %s arguments into %s.", $injected, $rcn), null, __METHOD__);
        }

        return $rawArgs;
    }

    /**
     * Combine arguments.
     * 
     * @param   array   $args1  First set.
     * @param   array   $args2  Second set.
     * @return  array
     */
    protected function combineArgs(array $args1, array $args2): array
    {
        return array_replace($args1, $args2);
    }

    /**
     * Bind parameters.
     * 
     * @param   \ReflectionClass    $reflectionClass    Class to bind to.
     * @param   array               $rawArgs            Raw arguments.
     * @return  array                                   Bound arguments. 
     */
    protected function bindConstructorParameters(\ReflectionClass $reflectionClass, array $rawArgs = []): array
    {
        $reflectionConstructor = $reflectionClass->getConstructor();

        if (is_null($reflectionConstructor)) {
            return $rawArgs;
        }

        // See if we can bind attributes.
        $rawArgs = $this->bindAttributeInjections($reflectionClass, $rawArgs);

        $args = [];
        $count = 0;

        foreach ($reflectionConstructor->getParameters() as $p) {

            // If we have an argument in this position, just use it.
            if (count($rawArgs) > 0 and isset($rawArgs[$count]) and !is_null($rawArgs[$count])) {
                $args[] = $rawArgs[$count];

            // Otherwise we have to do some tedious processing.
            } else {
                $reflectionParameterType = $p->getType();
                $found = null;  // Set to an ID if we find one.

                // If the reflection type can be used.
                if (!is_null($reflectionParameterType) and ($reflectionParameterType instanceof \ReflectionNamedType)) {

                    $typeName = $reflectionParameterType->getName();

                    // See if we can match the reflected type to a class we know about.
                    if (
                        !is_null($typeName) 
                        and !$reflectionParameterType->isBuiltin() 
                        and array_key_exists($typeName, $this->reverseClassMap)
                    ) {
                        $found = $this->reverseClassMap[$typeName];

                    // Don't give up with a class yet because we might implement the interface.
                    } else if (!is_null($typeName) and !$reflectionParameterType->isBuiltin()) {

                        foreach ($this->reverseClassMap as $className => $id) {
                        
                            $r = new \ReflectionClass($className);
                            $tr = new \ReflectionClass($typeName);
                            if ($tr->isInterface()) {
                                if ($r->implementsInterface($typeName)) {
                                    $found = $id;
                                    break;
                                }
                            }

                        }
                    
                    }

                    // That's the basics - now do some additional processing.

                    // If we found something above, resolve it.
                    if (!is_null($found)) {
                        $args[] = $this->resolve($found);

                    // Otherwise see if we can use the passed arguments.
                    } else if (count($rawArgs) > $count) {
                        $args[] = $rawArgs[$count];

                    // Otherwise see if we can use a default.
                    } else if ($p->isDefaultValueAvailable()) {
                        $args[] = $p->getDefaultValue();

                    // If all that is useless, set it to null.
                    } else {
                        $args[] = null;
                    }
                }
            }

            $count++;
        }

        return $args;
    }

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
    public function bind(string $id, array $args = [])
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf("No entry with ID '%s' could be found in the dependency injection container.",
                $id));
        }

        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->classMap)) {
            throw new NotFoundException(sprintf("No class entry with ID '%s' could be found in the dependency injection container.",
                $id));
        }

        // Process the entry.
        $entry = $this->classMap[$id];
        $className = $entry['class'];

        // Get the reflection.
        $reflectionClass = new \ReflectionClass($className);

        // Bind constructor parameters.
        $realArgs = $this->bindConstructorParameters($reflectionClass, $this->combineArgs($entry['args'], $args));

        // Create the new instance.
        $obj = null;
        if (0 == count($realArgs)) {
            $obj = new $className;
        } else {
            $obj = $reflectionClass->newInstanceArgs($realArgs);
        }

        if ($entry['singleton']) {
            if (array_key_exists($id, $this->instances)) {
                throw new AlreadyExistsException(sprintf("A singleton instance already exists for ID '%s'.", $id));
            }
            $this->instances[$id] = $obj;
            return $this->instances[$id];
        } else {
            return $obj;
        }
    }

    /**
     * Resolve an entry.
     * 
     * @param   string      $id         ID.
     * @param   array       $args       Arguments.
     * @return  mixed
     * @throws  NotFoundException
     * @throws  ResolveErrorException
     */
    protected function resolve(string $id, array $args = [])
    {
        // Try to resolve.
        //try {

            // Instance.
            if (array_key_exists($id, $this->instances)) {

                return $this->instances[$id];
            
            // Function.
            } else if (array_key_exists($id, $this->callableMap)) {

                return call_user_func_array(
                    $this->callableMap[$id]['callable'], 
                    $this->combineArgs($this->callableMap[$id]['args'], $args)
                );

            // Value.
            } else if (array_key_exists($id, $this->valueMap)) {

                return $this->valueMap[$id];
            
            // Class.
            } else if (array_key_exists($id, $this->classMap)) {

                return $this->bind($id, $this->combineArgs($this->classMap[$id]['args'], $args));

            } else {

                throw new NotFoundException(sprintf(
                    "No entry with ID '%s' could be found in the dependency injection container.",
                    $id));

            }

        //} catch (\Exception $ex) {
        //    throw new ResolveErrorException(sprintf("Error resolving entry with ID '%s':\n %s", $id, $ex->getMessage()), 0, $ex);
        //}
    }

    /**
     * See if we have an entry in the container.
     * 
     * @param   string      $id         ID of entry.
     * @return  bool    
     */
    public function has(string $id): bool
    {
        return (array_key_exists($id, $this->instances) or array_key_exists($id, $this->classMap) 
            or array_key_exists($id, $this->callableMap) or  array_key_exists($id, $this->valueMap));
    }

    /**
     * Get an entry from the container.
     * 
     * @param   string      $id         ID of entry.
     * @param   array       $args       Arguments.
     * @return  mixed
     */
    public function get(string $id, array $args = [])
    {
        return $this->resolve($id, $args);
    }

    /**
     * Quicker access to a singleton.
     * 
     * @param   string      $id         ID of entry.
     * @return  object
     */
    public function singleton(string $id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        return $this->resolve($id);
    }

    /**
     * Magic getter.
     * 
     * @param   string      $id         ID to get.
     * @return  mixed
     */
    public function __get(string $id)
    {
        return $this->get($id);
    }

    /**
     * Set the static instance.
     * 
     * @param   ContainerInterface  $instance   Instance to set.
     * @return  ContainerInterface
     */
    public static function setInstance(ContainerInterface $instance): ContainerInterface
    {
        static::$instance = $instance;
        return static::$instance; 
    }

    /**
     * Get the static instance.
     * 
     * @return  ContainerInterface
     */
    public static function getInstance(): ContainerInterface
    {
        return static::$instance; 
    }

    /**
     * Dump the container.
     * 
     * @return  array
     */
    public function dump(): array
    {
        $lines = [];
        $pad = 20;

        $instances = [];
        foreach ($this->instances as $k => $v) {
            $instances[$k] = get_class($v);
            if (strlen($k) > $pad) $pad = strlen($k);
        }
 
        $classes = [];
        foreach ($this->classMap as $k => $v) {
            $st = (true === $v['singleton']) ? "Singleton" : 'Class';
            $classes[$k] = $v['class'] . ', ' . $st;
            if (strlen($k) > $pad) $pad = strlen($k);
        }

        $values = [];
        foreach ($this->valueMap as $k => $v) {
            $values[$k] = $v;
            if (strlen($k) > $pad) $pad = strlen($k);
        }

        $callables = [];
        foreach ($this->callableMap as $k => $v) {
            $callables[$k] = gettype($v);
            if (strlen($k) > $pad) $pad = strlen($k);
        }

        $configs = [];
        foreach ($this->cfg->dump() as $k => $v) {
            $configs[$k] = $v;
            if (strlen($k) > $pad) $pad = strlen($k);
        }

        $pad++;

        $lines[] = "====================================================";
        $lines[] = "Dependency Injection Container Dump.";
        $lines[] = "====================================================";

        $lines[] = "-----------------------------";
        $lines[] = "Class Singleton Instances";
        $lines[] = "-----------------------------";

        foreach ($instances as $k => $v) {
            $lines[] = str_pad($k, $pad) . ': ' . $v;
        }

        $lines[] = "-----------------------------";
        $lines[] = "Class Definitions";
        $lines[] = "-----------------------------";

        foreach ($classes as $k => $v) {
            $lines[] = str_pad($k, $pad) . ': ' . $v;
        }

        $lines[] = "-----------------------------";
        $lines[] = "Value Definitions";
        $lines[] = "-----------------------------";

        foreach ($values as $k => $v) {
            $lines[] = str_pad($k, $pad) . ': ' . $v;
        }

        $lines[] = "-----------------------------";
        $lines[] = "Callable Definitions";
        $lines[] = "-----------------------------";

        foreach ($callables as $k => $v) {
            $lines[] =str_pad($k, $pad) . ': ' . $v;
        }

        $lines[] = "-----------------------------";
        $lines[] = "Configs";
        $lines[] = "-----------------------------";

        foreach ($configs as $k => $v) {
            $lines[] = str_pad($k, $pad) . ': ' . $v;
        }

        $lines[] = "====================================================";

        return $lines;
    }
}
