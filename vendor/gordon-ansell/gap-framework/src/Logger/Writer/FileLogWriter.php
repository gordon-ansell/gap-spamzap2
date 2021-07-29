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
namespace GreenFedora\Logger\Writer;

use GreenFedora\Logger\Writer\Exception\RuntimeException;
use GreenFedora\Logger\Writer\AbstractLogWriter;
use GreenFedora\Logger\Writer\LogWriterInterface;
use GreenFedora\Logger\Formatter\LogFormatterInterface;

/**
 * File log writer.
 */
class FileLogWriter extends AbstractLogWriter implements LogWriterInterface
{
	/**
	 * Path to write to.
	 * @param string
	 */
	protected $path = null;
	
	/**
	 * Constructor.
	 *
	 * @param 	iterable					$cfg 				Configs.
	 * @param 	LogFormatterInterface|null	$formatter			Log message formatter.
	 * @param 	string|null 				$path				Path to write to.
	 *
	 * @return	void
     * 
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null, ?LogFormatterInterface $formatter = null, ?string $path = null)	
	{
		parent::__construct($cfg, $formatter);
		
		if (is_null($path)) {
			if (array_key_exists('path', $this->cfg->toArray())) {
				$this->path = $this->cfg->path;
			} else {
				$this->path = rtrim(app()->getConfig('env.BASEPATH'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'logs'; 
			}
		} else {
			$this->path = $path;
		}	
		
		$this->checkLogPath();
		$this->cleanLogDir();
	}
	
	/**
	 * Check the log path.
	 *
	 * @return 	bool
	 *
	 * @throws	RuntimeException	If the log path is null.
	 * @throws	RuntimeException	If the log path can't be found and we can't create it.
	 */
	protected function checkLogPath() : bool
	{
		if (is_null($this->path)) {
			if (false === mkdir($this->path, 0777, true)) {
				throw new RuntimeException(sprintf("Log path is null"));
				return false;
			}
		}
		
		if (!file_exists($this->path)) {
			if (false === mkdir($this->path, 0777, true)) {
				throw new RuntimeException(sprintf("Unable to create log directory at %s", $this->path));
				return false;
			}
		}
		return true;
	}	
	
	/**
	 * Clean the log directory.
	 *
	 * @return 	void
	 */
	protected function cleanLogDir()
	{
		$dt = new \DateTime();
		$dt->sub(new \DateInterval('P' . $this->cfg->retainDays . 'D'));
		$dtLimit = $dt->format('Y-m-d');
		
		foreach (new \DirectoryIterator($this->path) as $fileInfo) {
			if ($fileInfo->isDot()) {
				continue;
			}
			if ($fileInfo->getBasename() < $dtLimit) {
				unlink($fileInfo->getPathname());
			}
		}	
	}		   	 

	/**
	 * Write a log message.
	 *
	 * @param 	string 				$msg 		Message to write.
	 * @param 	int  				$level 		Level of message.
	 * @param 	mixed 				$context	Message context.
	 * @param 	mixed 				$section 	Section.
	 *
	 * @return 	void
	 *
	 * @throws 	RuntimeException 	If we have any problems at all.
	 */
	public function write(string $msg, int $level, $context = null, ?string $section = null)
	{
		$dt = new \DateTime();
		$logFilePath = $this->path . DIRECTORY_SEPARATOR . $dt->format('Y-m-d') . '.log';
		$logFile = fopen($logFilePath, 'a');
		if (false === $logFile) {
			throw new RuntimeException(sprintf("Unable to open log file at: %s", $logFilePath));
		}
		if (false === fwrite($logFile, $this->formatter->format($msg, $level, $context, $section) . PHP_EOL)) {
			fclose($logFile);
			throw new RuntimeException(sprintf("Unable to write to log file at: %s", $logFilePath));
		} 
		fclose($logFile);
	}	
}
