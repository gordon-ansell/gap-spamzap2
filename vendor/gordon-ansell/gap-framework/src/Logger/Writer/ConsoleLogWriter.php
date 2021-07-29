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
use GreenFedora\Logger\Writer\AbstractLogWriter;
use GreenFedora\Logger\Writer\LogWriterInterface;
use GreenFedora\Stdlib\Level;
use GreenFedora\Logger\Formatter\LogFormatterInterface;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Console\Output\ConsoleColourDecorator;

/**
 * Console log writer.
 */
class ConsoleLogWriter extends AbstractLogWriter implements LogWriterInterface
{
    /**
     * The console stream.
     * @var resource
     */
    protected $stream = null;

	/**
	 * Constructor.
	 *
	 * @param 	iterable|null				$cfg 				Configs.
	 * @param 	LogFormatterInterface|null	$formatter			Log message formatter.
	 *
	 * @return	void
     * 
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null, ?LogFormatterInterface $formatter = null)	
	{
        parent::__construct($cfg, $formatter);
        $this->stream = @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');
	}
	/**
	 * Write a log message.
	 *
	 * @param 	string 				$msg 		Message to write.
	 * @param 	int 				$level 		Level of message.
	 * @param 	mixed 				$context	Message context.
	 * @param 	mixed 				$section 	Section.
	 *
	 * @return 	void
	 */
	public function write(string $msg, int $level, $context = null, ?string $section = null)
	{
        $decorator = new ConsoleColourDecorator();
		$msg = $this->formatter->format($msg, $level, $context, $section);
        $msg = $decorator->decorate($msg, $level);
        @fwrite($this->stream, $msg . PHP_EOL);
        fflush($this->stream);
	}	
}
