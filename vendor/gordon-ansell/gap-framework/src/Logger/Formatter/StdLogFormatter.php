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
namespace GreenFedora\Logger\Formatter;

use GreenFedora\Logger\Formatter\LogFormatterInterface;
use GreenFedora\Logger\Formatter\Exception\InvalidArgumentException;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Stdlib\Level;

/**
 * Standard log formatter.
 */
class StdLogFormatter implements LogFormatterInterface
{
	/**
	 * Configs from the logger.
	 * @var array
	 */
	protected $defaults = array(
		'level'			=>	Level::NOTICE,
		'dtFormat'		=>	'Y-m-d H:i:s.v',
		'retainDays'	=>	30,				
	);	
	
	/**
	 * Configs.
	 * @var Arr
	 */
	protected $cfg = null;	

	/**
	 * Constructor.
	 *
	 * @param 	iterable|null	$cfg 	Configs.
	 *
	 * @return	void
     * 
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null)	
	{
		$this->cfg = new Arr($this->defaults);

        if (null === $cfg) {
			throw new InvalidArgumentException("Logger config is null.");
        }
		$this->cfg = $this->cfg->mergeReplaceRecursive($cfg);
	}
	
	/**
	 * Format the message.
	 *
	 * @param 	string 				$msg 		Message to format.
	 * @param 	int  				$level 		Level of message.
	 * @param 	mixed 				$context	Message context.
	 * @param 	mixed 				$section 	Section.
	 *
	 * @return 	string
	 */	
	public function format(string $msg, int $level, $context = null, ?string $section = null) : string
	{
		$dt = new \DateTime();

		$ret = null;
		if (is_null($section)) {
			$ret = sprintf("%s %s %s", 
				$dt->format($this->cfg->dtFormat),
				strtoupper(str_pad(Level::l2t($level), 10)),
				$msg
			);
		} else {
			$ret = sprintf("%s %s %s [[%s]]", 
				$dt->format($this->cfg->dtFormat),
				strtoupper(str_pad(Level::l2t($level), 10)),
				$msg,
				$section
			);
		}
		return $ret;		
	}
}
