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
namespace Gf\Console\Command;

use GreenFedora\Console\Command\Command;
use GreenFedora\Stdlib\Path;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Logger\LoggerInterface;
use Gf\Domain\Interfacer;
use GreenFedora\GetOpt\Parameter;
use GreenFedora\Console\ConsoleApplicationInterface;

/**
 * Interfacer command.
 * 
 * #[Inject (appPath: appPath)]
 * #[Inject (gfstart: greenfedora_startup_path)]
 * #[Inject (codeDir: cfg|locations.code)]
 */
class InterfacerCommand extends Command
{   
    /**
     * Application path.
     * @var string
     */ 
    protected $appPath = null;

    /**
     * Greenfedora startup path.
     * @var string
     */
    protected $gfstart = null;

    /**
     * Code directory.
     * @var string
     */
    protected $codeDir = null;

    /**
     * Constructor.
     * 
     * @param   OutputInterface      $output                    Output.
     * @param   LoggerInterface      $logger                    Logger.
     * @param   ConsoleApplicationInterface     $app        Parent application.
     * @param   string|null          $appPath                   Application path.
     * @param   string|null          $gfstart                   Startup path.
     * @param   string|null          $codeDir                   Code directory.
     * @return  void
     */
    public function __construct(
        ?OutputInterface $output = null, 
        LoggerInterface $logger = null, 
        ConsoleApplicationInterface $app = null,        
        ?string $appPath = null, 
        ?string $gfstart = null,
        ?string $codeDir = null
    )
    {
        parent::__construct($output, $logger, $app);
        $this->appPath = $appPath;
        $this->gfstart = $gfstart;
        $this->codeDir = $codeDir;
    }

    /**
     * Initialisation.
     * 
     * @return void
     */
    public function init()
    {
        $this->setName('interface')
            ->setDescription('Extract an interface from a class file.')
            ->addPositional('command', "The interface command.", Parameter::HELPIGNORE)
            ->addPositional('classfile', "Class file to extract interface from.")
            ->addOption('help', 'Show this help.', null, 'h');
    }

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int
    {
        // Get the path.
        $this->debug("Root path: " . $this->gfstart, null, __METHOD__);
        $this->debug("App path: " . $this->appPath, null, __METHOD__);

        // Get the classfile argument.
        $classfile = $this->getPositional('classfile')->getValue();
        $this->debug("Classfile: " . $classfile, null, __METHOD__);

        // Mode.
        $mode = 'fw';
 
        // Create the interfacer and call it.
        $interfacer = new Interfacer($this->appPath, $this->codeDir, $mode, $this->logger);

        $ret = $interfacer->extractInterface($classfile);

        if (0 === $ret) {
            $msgs = $interfacer->getInfoMsgs();
            foreach ($msgs as $msg) {
                $this->output->info($msg);
            }
        } else {
            $this->output->error($interfacer->getLastError());
        }

        $this->output->blank()->notice("Successfully generated an interface from class file '%s'.", [$classfile]);
        return 0;
    }
}
