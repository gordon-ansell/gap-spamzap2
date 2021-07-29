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
namespace GreenFedora\Logger;

use GreenFedora\Application\ExceptionFormatter;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\Level;
use GreenFedora\Logger\Exception\InvalidArgumentException;
use GreenFedora\Logger\Writer\LogWriterInterface;
use GreenFedora\Stdlib\Arr\Arr;

/**
 * Logger.
 *
 * Processes messages.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Logger implements LoggerInterface
{	    
    /**
	 * Error counts.
	 * @var int[]
	 */
	protected $msgCounts = array(
		Level::EMERGENCY	=>	0,
		Level::ALERT		=>	0,
		Level::CRITICAL	    =>	0,
		Level::ERROR 	    =>	0,
		Level::WARNING	    =>	0,
		Level::NOTICE	    =>	0,
		Level::INFO		    =>	0,
		Level::DEBUG 	    =>	0,			
	);  
	
	/**
	 * Log levels allowed.
	 * @var string[]
	 */
	protected $allowedLevels = array(
		Level::EMERGENCY, Level::ALERT, Level::CRITICAL, Level::ERROR, Level::WARNING,
		Level::NOTICE, Level::INFO, Level::DEBUG
	);	

    /**
     * Sections.
     * @var mixed
     */
    protected $sections = null;
	
	/**
	 * Log configs.
	 * @var array
	 */
	protected $defaults = array(
		'level'			=>	Level::NOTICE,
		'dtFormat'		=>	'Y-m-d H:i:s.v',
		'retainDays'	=>	30,		
        'sections'      =>  null
	);	
	
	/**
	 * Configs.
	 * @var Arr
	 */
	protected $cfg = null;	 
	
	/**
	 * Log writers.
	 * LogWriterInterface[]
	 */
	protected $writers = array();	
	
	/**
	 * Constructor.
	 *
	 * @param 	iterable|null	$cfg 		        Log configs.
	 * @param 	iterable|null	$writers            Log writers.
	 *
	 * @return 	void
	 *
	 * @throws	InvalidArgumentException	
     * 
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null, ?iterable $writers = null)
	{
		$this->cfg = new Arr($this->defaults);
        if (null === $cfg) {
			throw new InvalidArgumentException("Logger config is null.");
        }
		$this->cfg = $this->cfg->mergeReplaceRecursive($cfg);

        if ($this->cfg->has('sections') and !is_null($this->cfg->get('sections'))) {
            $this->displaySections($this->cfg->sections->toArray());
        }

        if (null === $writers) {
			throw new InvalidArgumentException("Log writers are null.");
        }
		$this->writers = $writers;
		foreach ($this->writers as $writer) {
			if (!$writer instanceof LogWriterInterface) {
				throw new InvalidArgumentException(sprintf("Log writers passed to the Logger must implement LogWriterInterface, you passed type '%s'", gettype($writer)));
			}
		}
		if (!$this->checkLevel($this->cfg->level)) {
			throw new InvalidArgumentException(sprintf("Log level '%s' is not allowed.", $this->cfg->level));
		}
	}

    /**
     * Control the display of sections.
     * 
     * @param   mixed   $sections   Sections.
     * @return  LoggerInterface
     */
    public function displaySections($sections): LoggerInterface
    {
        if ('all' == $sections) {
            $this->sections = 'all';
        } else if (is_array($sections) and count($sections) > 0) {
            $this->sections = $sections;
        } else {
            $this->sections = null;
        }
        return $this;
    }
	
	/**
	 * Check a log level is valid.
	 *
	 * @param 	in  		$level 	Level to check.
	 *
	 * @return 	bool
	 */
	protected function checkLevel(int $level) : bool
	{
		return in_array($level, $this->allowedLevels);
	}	
	
	/**
	 * Get or set the log level.
	 *
	 * @param 	mixed|null		$level		Log level to set or null for a fetch.
	 *
	 * @return 	int 		The current level if we fetch, otherwise the old level.
	 *
	 * @throws	InvalidArgumentException	If the log level is not allowed.
	 */
	public function level($level = null) : int
	{
		if (is_null($level)) {
			return $this->cfg->level;
		}

        if (is_string($level)) {
            $level = Level::t2l($level);
        }
		
		if (!$this->checkLevel($level)) {
			throw new InvalidArgumentException(sprintf("Log level '%i' is not allowed.", $level));
		}

		$oldLevel = $this->cfg->level;
		$this->cfg->level = $level;
		return $oldLevel;
	}
	
	/**
	 * Get the message count for a level or levels.
	 *
	 * @param 	string		$level		Level or levels to get count for. Null returns all.			
	 *
	 * @return	int
	 */
	public function getMessageCount(string $level) : int
	{
		if (false !== strpos($level, '|')) {
			$levels = explode('|', $level);
			$ret = 0;
			foreach ($levels as $test) {
                $num = Level::t2l($test);
				if (array_key_exists($num, $this->msgCounts)) {
					$ret += $this->msgCounts[$num];
				}
			}			
			return $ret;
		} else {
			return $this->msgCounts[Level::t2l($level)];
		}
	}
	
	/**
	 * Get all the message counts.
	 *
	 * @return 	int[]
	 */
	public function getMessageCounts() : array
	{
		return $this->msgCounts;
	}	

    /**
     * See if we have a section starting with the passed thing.
     * 
     * @param   string|null  $check  What to check.
     * @return  bool
     */
    protected function sectionHas(?string $check): bool
    {
        if (is_null($check) or 'all' === $this->sections) {
            return true;
        }

        if (is_null($this->sections)) {
            return false;
        }

        if (is_array($this->sections)) {
            if (0 === count($this->sections)) {
                return false;
            }
            foreach ($this->sections as $sect) {
                if (str_starts_with($check, $sect)) {
                    return true;
                }
            }
        }
        return false;
    }
	 
    /**
     * Logs with an arbitrary level.
     *
     * @param 	mixed  	$level			Logging level.
     * @param 	string 	$message		Message string.
     * @param 	mixed  	$context		Additional context.
     * @param   string  $section        Section.
     *
     * @return 	void
     */
    public function log($level, $message, $context = null, ?string $section = null)
    {
        $show = $this->sectionHas($section);
        if (!$show) {
            return;
        }

        if (is_string($level)) {
            $level = Level::t2l($level);
        }
		if ($level >= $this->cfg->level) {
			foreach ($this->writers as $writer) {
				$writer->write($message, $level, $context, $section);
			}
			$this->msgCounts[$level]++;
		}   
        
        if (!is_null($context) and ($context instanceof \Throwable)) {
            $lines = ExceptionFormatter::format($context);
			foreach ($this->writers as $writer) {
                foreach ($lines as $line) {
                    $writer->write($line, Level::CRITICAL, null, $section);
                }
            }
        }
    }
 
    /**
     * System is unusable.
     *
     * @param string    $message
     * @param array     $context
     *
     * @return void
     */
    public function emergency($message, $context = null, ?string $section = null)
    {
        $this->log(Level::EMERGENCY, $message, $context, $section);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function alert($message, $context = null, ?string $section = null)
    {
        $this->log(Level::ALERT, $message, $context, $section);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function critical($message, $context = null, ?string $section = null)
    {
        $this->log(Level::CRITICAL, $message, $context, $section);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function error($message, $context = null, ?string $section = null)
    {
        $this->log(Level::ERROR, $message, $context, $section);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function warning($message, $context = null, ?string $section = null)
    {
        $this->log(Level::WARNING, $message, $context, $section);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function notice($message, $context = null, ?string $section = null)
    {
        $this->log(Level::NOTICE, $message, $context, $section);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function info($message, $context = null, ?string $section = null)
    {
        $this->log(Level::INFO, $message, $context, $section);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param mixed $context
     *
     * @return void
     */
    public function debug($message, $context = null, ?string $section = null)
    {
        $this->log(Level::DEBUG, $message, $context, $section);
    }

}
