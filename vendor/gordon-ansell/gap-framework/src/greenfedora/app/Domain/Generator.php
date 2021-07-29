<?php
/**
 * This file is part of the Gf package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Gf\Domain;

use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\Path;

/**
 * Generator class.
 */
class Generator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Last error.
     * @var string|null
     */
    protected $lastError = null;

    /**
     * Info messages.
     * @var array
     */
    protected $infoMessages = [];

    /**
     * Application path.
     * @var string
     */
    protected $appPath = null;

    /**
     * Code path.
     * @var string
     */
    protected $codePath = null;

    /**
     * Test path.
     * @var string
     */
    protected $testPath = null;

    /**
     * Generated exceptions?
     * @var bool
     */
    protected $genex = true;

    /**
     * Generated tests?
     * @var bool
     */
    protected $gentests = true;

    /**
     * Mode.
     * @var string
     */
    protected $mode = null;

    /**
     * Compound subcomponents?
     * @var bool
     */
    protected $compound = false;

    /**
     * Constructor.
     * 
     * @param   string              $appPath    Application path.
     * @param   string              $codePath   Where to put code.
     * @param   string              $testPath   Where to put tests.
     * @param   bool                $genex      Generate exceptions?
     * @param   bool                $gentests   Geneerate tests?
     * @param   string              $mode       Framework or app?
     * @param   bool                $compound   Compound subs?
     * @param   LoggerInterface     $logger     The logger.
     * @return  void
     */
    public function __construct(string $appPath, string $codePath, string $testPath, 
    bool $genex, bool $gentests, string $mode, bool $compound, LoggerInterface $logger)
    {
        $this->appPath = $appPath;
        $this->logger = $logger;
        $this->codePath = $codePath;
        $this->testPath = $testPath;
        $this->genex = $genex;
        $this->gentests = $gentests;
        $this->mode = $mode;
        $this->compound = $compound;
    }

    /**
     * Get the last error.
     * 
     * @return  string|null
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Get the info messages.
     * 
     * @return  array
     */
    public function getInfoMsgs(): array
    {
        return $this->infoMessages;
    }

    /**
     * Do the string replacements.
     * 
     * @param   string  $source     Source string.
     * @param   string  $component  Component name.
     * @param   string  $classname  Name of class.
     * @param   string  $namespace  The namespace.
     * @param   string  $interface  Interface suffix.
     * @return  string
     */
    protected function doReplacements(string $source, string $component, string $classname, 
    string $namespace, string $interface = 'Interface'): string
    {
        $reps = array(
            'namespace'      =>  $namespace,
            'component'      =>  str_replace("/", "\\", $component),
            'classname'      =>  $classname,
            'interface'      =>  $classname . $interface
        );

        $ret = $source;

        foreach ($reps as $k => $v) {
            $ret = str_replace('<' . $k . '>', $v, $ret);
        }

        return $ret;
    }

    /**
     * Create a file from a stub.
     * 
     * @param   string  $stub       Stub file.
     * @param   string  $op         Output file.
     * @param   string  $component  Component.
     * @param   string  $classname  Name of class.
     * @param   string  $namespace  The namespace.
     * @param   string  $interface  Interface suffix.
     * 
     * @return  bool 
     */
    protected function createFromStub(string $stub, string $op, string $component, string $classname,
    string $namespace, string $interface = 'Interface'): bool
    {
        $data = file_get_contents($stub);

        if (false === $data) {
            $this->lastError = sprintf("Cannot get contents of: %s.", $stub);
            return false;
        }

        if (false === file_put_contents($op, $this->doReplacements($data, $component, $classname, $namespace, $interface))) {
            $this->lastError = sprintf("Cannot put contents into: %s.", $op);
            return false;
        }

        return true;
    } 

    /**
     * Process a directory.
     * 
     * @param   string  $source     Source directory.
     * @param   string  $target     Target directory.
     * @param   string  $component  Component.
     * @param   string  $classname  Name of class.
     * @param   string  $namespace  The namespace.
     * @param   string  $interface  Interface suffix.
     * 
     * @return  bool
     */
    protected function dirCopy(string $source, string $target, string $component, string $classname,
    string $namespace, string $interface = 'Interface'): bool
    {
        if (!file_exists($source)) {
            $this->lastError = sprintf("Cannot copy directory, source directory does not exist: %s.", $source);
            return false;
        }
        if (file_exists($target)) {
            $this->lastError = sprintf("Cannot copy directory, target directory already exists: %s.", $target);
            return false;
        }

        if (false === mkdir($target, 0777, true)) {
            $this->lastError = sprintf("Cannot create component '%s', cannot create directory: %s.", $component, $target);
            return false;
        }

        foreach (new \DirectoryIterator($source) as $fi) {
            if (!$fi->isDot()) {
                $fo = Path::join($target, $fi->getFilename());
                if (!$this->createFromStub($fi->getPathname(), $fo, $component, $classname, $namespace, $interface)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Do the file generations.
     * 
     * @param   string  $component  Component.
     * @param   string  $namespace  The namespace.
     * @param   string  $interface  Interface suffix.
     * 
     * @return  int
     */
    public function doFileGenerations(string $component, string $namespace, $interface = 'Interface'): int
    {
        // Check the paths are vaild.
        if (file_exists($this->codePath)) {
            $this->lastError = sprintf("Cannot create component '%s', path already exists at: %s.", 
                $component, $this->codePath);
            return 1;
        }
        if (false === mkdir($this->codePath, 0777, true)) {
            $this->lastError = sprintf("Cannot create component '%s', cannot create directory: %s.", 
                $component, $this->codePath);
            return 1;
        }

        // Class/interface name.
        $classname = $component;
        if ($this->compounds) {
            $classname = str_replace('/', '', $classname);
        } else if (false !== strpos($component, "/")) {
            $tmp = explode("/", $classname);
            $classname = $tmp[sizeof($tmp) - 1];
        }

        $resourcesDir = Path::join($this->appPath, 'resources', 'stubs', $this->mode); 
        $this->debug('Resources dir: ' . $resourcesDir, null, __METHOD__);

        // Class file.
        $tpl = Path::join($resourcesDir, 'class.php');
        $op = Path::join($this->codePath, $classname . '.php');
        if (false === $this->createFromStub($tpl, $op, $component, $classname, $namespace, $interface)) {
            return 1;
        }
        $this->infoMessages[] = sprintf("Created class file for '%s'.", $classname);
        
        // Interface file.
        $tpl = Path::join($resourcesDir, 'interface.php');
        $op = Path::join($this->codePath, $classname . $interface . '.php');
        if (false === $this->createFromStub($tpl, $op, $component, $classname, $namespace, $interface)) {
            return 1;
        }
        $this->infoMessages[] = sprintf("Created interface file for '%s'.", $classname . $interface);

        // Exceptions.
        if ($this->genex) {
            $tpl = Path::join($resourcesDir, 'exceptions');
            $op = Path::join($this->codePath, 'Exception');
            if (false === $this->dirCopy($tpl, $op, $component, $classname, $namespace, $interface)) {
                return 1;
            }
            $this->infoMessages[] = "Created all exceptions.";
        }

        // Tests.
        if ($this->gentests) {
            $tpl = Path::join($resourcesDir, 'test.php');
            if (file_exists($this->testPath)) {
                $this->lastError = sprintf("Cannot create component tests '%s', output directory already exists.", $component);
                return 1;
            }
            if (false === mkdir($this->testPath, 0777)) {
                $this->lastError = sprintf("Cannot create component tests '%s', cannot create directory: %s.", $component, $this->testPath);
                return 1;
            }
            $op = Path::join($this->testPath, $classname . "Test.php");  
            if (false === $this->createFromStub($tpl, $op, $component, $classname, $namespace, $interface)) {
                return 1;
            }
            $this->infoMessages[] = "Created tests.";
        }

        return 0;
    }

}
