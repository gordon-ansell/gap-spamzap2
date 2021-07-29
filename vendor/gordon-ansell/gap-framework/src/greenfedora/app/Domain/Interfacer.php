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
 * Interface class.
 */
class Interfacer implements LoggerAwareInterface
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
     * @param   string              $mode       Framework or app?
     * @param   LoggerInterface     $logger     The logger.
     * @return  void
     */
    public function __construct(string $appPath, string $codePath, string $mode, LoggerInterface $logger)
    {
        $this->appPath = $appPath;
        $this->logger = $logger;
        $this->codePath = $codePath;
        $this->mode = $mode;
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
     * Ignore this line?
     * 
     * @param   string      $line       Line to test.
     * @return  bool
     */
    protected function ignoreThisLine(string $line): bool
    {
        $igs = array('__construct', '__get', '__set', '__invoke', 'static ');

        foreach ($igs as $ig) {
            if (false !== strpos($line, $ig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract and interface.
     * 
     * @param   string      $classFile  Class we're extracting from.
     * @return  int 
     */
    public function extractInterface(string $classFile): int
    {
        $path = Path::join($this->codePath, $classFile);        
        $lines = file($path, FILE_SKIP_EMPTY_LINES);

        $startComment = '/**';
        $endComment = '*/';

        $commentBuffer = [];

        $collected = [];

        $inClass = false;
        $inComment = false;

        foreach ($lines as $line) {
            $line = rtrim($line);
            
            if (!$inClass and 'class ' === substr(trim($line), 0, strlen('class '))) {
                $inClass = true;
            }
            if ($inClass) {
                if (!$inComment and $startComment === substr(trim($line), 0, strlen($startComment))) {
                    $commentBuffer = [$line];
                    $inComment = true;
                    continue;
                } else if ($inComment and $endComment === substr(trim($line), 0, strlen($endComment))) {
                    $commentBuffer[] = $line;
                    $inComment = false;
                    continue;
                } else if ($inComment) {
                    $commentBuffer[] = $line;
                    continue;
                }

                if (false !== strpos($line, 'function ') 
                and false !== strpos($line, 'public ')
                and !$this->ignoreThisLine($line)
                ) {
                    $tmp = [];
                    foreach ($commentBuffer as $comment) {
                        $tmp[] = $comment;
                    }
                    $tmp[] = $line . ';';
                    $collected[] = $tmp;
                }
            }
        }

        if (count($collected) > 0) {
            echo "============================= Copy between here ..." . PHP_EOL;
            foreach ($collected as $coll) {
                foreach ($coll as $line) {
                    echo $line . PHP_EOL;
                }
                echo PHP_EOL;
            }
            echo "============================= ... and here.";
        } else {
            $this->infoMsgs[] = "Nothing to extract.";
        }


        return 0;
    }

}
