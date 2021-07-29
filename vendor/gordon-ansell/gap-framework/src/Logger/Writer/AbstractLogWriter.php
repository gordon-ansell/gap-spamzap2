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

use GreenFedora\Logger\Writer\Exception\InvalidArgumentException;
use GreenFedora\Stdlib\Level;
use GreenFedora\Logger\Formatter\LogFormatterInterface;
use GreenFedora\Stdlib\Arr\Arr;

/**
 * Abstract log writer.
 */
abstract class AbstractLogWriter
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
	 * Log formatter.
	 * @var LogFormatterInterface
	 */
	protected $formatter = null;	

	/**
	 * Constructor.
	 *
	 * @param 	iterable|null				$cfg        		Configs.
	 * @param 	LogFormatterInterface|null	$formatter			Log message formatter.
	 *
	 * @return	void
	 * 
	 * @throws  InvalidArgumentException
     * 
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null, ?LogFormatterInterface $formatter = null)	
	{
		$this->cfg = new Arr($this->defaults);

        if (null === $cfg) {
			throw new InvalidArgumentException("Logger config is null.");
        }
		$this->cfg = $this->cfg->mergeReplaceRecursive($cfg);
        if (null === $formatter) {
			throw new InvalidArgumentException("Log formatter is null.");
        }
		$this->formatter = $formatter;
	}
}
