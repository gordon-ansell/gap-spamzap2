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
namespace GreenFedora\Config;

use GreenFedora\Config\ConfigInterface;
use GreenFedora\Stdlib\Arr\ArrIter;
use GreenFedora\Stdlib\Arr\DottedArr;
use GreenFedora\Finder\Finder;
use GreenFedora\Config\Reader\PhpConfigReader;

/**
 * Config class.
 */
class Config extends DottedArr implements ConfigInterface
{
	/**
	 * Which subdirs to process for each mode.
	 * @var array
	 */
	protected $modeSubDirs = array(
		'prod'		=>	array('.'),
		'staging'	=>	array('.', 'staging'),
		'dev'		=>	array('.', 'staging', 'dev'),	
	);
	
	/**
	 * Config file extensions we handle.
	 * @var array
	 */
	protected $configExts = array(
		'php',	
	);	

    /**
     * Base path.
     * @var string
     */
    protected $basePath = null;
	
	/**
	 * Constructor.
	 *
	 * @param	iterable	$input 				Either an array or an object.
	 * @param 	int 		$flags 				As per \ArrayObject.
	 * @param 	string 		$iteratorClass		Class to use for iterators.
	 *
	 * @return	void
	 */
	public function __construct(iterable $input = array(), int $flags = 0, string $iteratorClass = ArrIter::class)
	{
		parent::__construct($input, $flags, $iteratorClass);
	}

	/**
	 * Set the base path.
	 * 
	 * @param 	string 	$basePath 	Base path to set.
	 * @return 	ConfigInterface
	 */
	public function setBasePath(string $basePath): ConfigInterface
	{
		$this->basePath = $basePath;
		return $this;
	}
	
	/**
	 * Process passed paths for configs.
	 *
	 * Any files in the path are potential config files and we determine what type by their extension:
	 *
	 *	-	.php indicates a PHP config file that will simply be 'required'.
	 *
	 * Subdirectories on each path can be named 'dev' or 'staging' and are only processed if the given mode
	 * is passed in.
	 *
	 * Paths are processed in the order they're passed in with later values overwriting older ones. However,
	 * if the mode is 'dev' or 'test' then these subdirectories are processed after the main directory.
	 *
	 * @param 	string|null 		$mode 		The run mode, which will be 'dev', 'test' or 'prod'.
	 * @param 	array|string|null	$paths		A single path string, an array of paths or null just to use the base app path.
	 *
	 * @return	ConfigInterface
	 */
	public function process(?string $mode = 'prod', $paths = null): ConfigInterface
	{
		if (is_null($mode)) {
			$mode = 'prod';
		}
		// Turn the paths into something iterable.
		if (is_null($paths)) {
			$paths = array($this->basePath . DIRECTORY_SEPARATOR . 'config');
		} else if (!is_array($paths)) {
			$paths = array($paths);
		}
				
		// Process each mode.
		foreach ($this->modeSubDirs[$mode] as $modeToProcess) {
			// Process each path for a particular mode.
			foreach ($paths as $path) {
				if ('.' == $modeToProcess) {
					$pathToProcess = $path;
				} else {
					$pathToProcess = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $modeToProcess;
				}
			}
			
			// Check if the directory exists and, if so, process it.
			if (file_exists($pathToProcess)) {
				$this->processPath($pathToProcess);
			}
		} 		
		
		return $this;
	}
	
	/**
	 * Process an individual path.
	 *
	 * @param 	string 			$path 		Path to be processed.
	 *
	 * @return 	void
	 */
	protected function processPath(string $path)
	{
		foreach ((new Finder($path))->filter(true) as $entry) {

			//echo '>>>>>' . $entry->getPathname() . PHP_EOL;
			
			if (in_array($entry->getExtension(), $this->configExts)) {

				$reader = null;

				switch ($entry->getExtension()) {
					case 'php':
						$reader = new PhpConfigReader();
                        break;
				}
				
				$this->mergeReplaceRecursive($reader->read($entry->getPathname()));
			}
		}	
	}
}
